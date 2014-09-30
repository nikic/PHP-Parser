<?php

namespace PhpParser;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Lexer */
    protected $lexer;

    protected function setUp() {
        $this->lexer = new Lexer;
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $message) {
        try {
            $this->lexer->startLexing($code);
        } catch (Error $e) {
            $this->assertSame($message, $e->getMessage());

            return;
        }

        $this->fail('Expected PhpParser\Error');
    }

    public function provideTestError() {
        return array(
            array('<?php /*', 'Unterminated comment on line 1'),
            array('<?php ' . "\1", 'Unexpected character "' . "\1" . '" (ASCII 1) on unknown line'),
            array('<?php ' . "\0", 'Unexpected null byte on unknown line'),
        );
    }

    /**
     * @dataProvider provideTestLex
     */
    public function testLex($code, $tokens) {
        $this->lexer->startLexing($code);
        while ($id = $this->lexer->getNextToken($value, $startAttributes, $endAttributes)) {
            $token = array_shift($tokens);

            $this->assertSame($token[0], $id);
            $this->assertSame($token[1], $value);
            $this->assertEquals($token[2], $startAttributes);
            $this->assertEquals($token[3], $endAttributes);
        }
    }

    public function provideTestLex() {
        return array(
            // tests conversion of closing PHP tag and drop of whitespace and opening tags
            array(
                '<?php tokens ?>plaintext',
                array(
                    array(
                        Parser::T_STRING, 'tokens',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        ord(';'), '?>',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        Parser::T_INLINE_HTML, 'plaintext',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                )
            ),
            // tests line numbers
            array(
                '<?php' . "\n" . '$ token /** doc' . "\n" . 'comment */ $',
                array(
                    array(
                        ord('$'), '$',
                        array('startLine' => 2), array('endLine' => 2)
                    ),
                    array(
                        Parser::T_STRING, 'token',
                        array('startLine' => 2), array('endLine' => 2)
                    ),
                    array(
                        ord('$'), '$',
                        array(
                            'startLine' => 3,
                            'comments' => array(new Comment\Doc('/** doc' . "\n" . 'comment */', 2))
                        ),
                        array('endLine' => 3)
                    ),
                )
            ),
            // tests comment extraction
            array(
                '<?php /* comment */ // comment' . "\n" . '/** docComment 1 *//** docComment 2 */ token',
                array(
                    array(
                        Parser::T_STRING, 'token',
                        array(
                            'startLine' => 2,
                            'comments' => array(
                                new Comment('/* comment */', 1),
                                new Comment('// comment' . "\n", 1),
                                new Comment\Doc('/** docComment 1 */', 2),
                                new Comment\Doc('/** docComment 2 */', 2),
                            ),
                        ),
                        array('endLine' => 2)
                    ),
                )
            ),
            // tests differing start and end line
            array(
                '<?php "foo' . "\n" . 'bar"',
                array(
                    array(
                        Parser::T_CONSTANT_ENCAPSED_STRING, '"foo' . "\n" . 'bar"',
                        array('startLine' => 1), array('endLine' => 2)
                    ),
                )
            ),
        );
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function testHandleHaltCompiler($code, $remaining) {
        $this->lexer->startLexing($code);

        while (Parser::T_HALT_COMPILER !== $this->lexer->getNextToken());

        $this->assertSame($this->lexer->handleHaltCompiler(), $remaining);
        $this->assertSame(0, $this->lexer->getNextToken());
    }

    public function provideTestHaltCompiler() {
        return array(
            array('<?php ... __halt_compiler();Remaining Text', 'Remaining Text'),
            array('<?php ... __halt_compiler ( ) ;Remaining Text', 'Remaining Text'),
            array('<?php ... __halt_compiler() ?>Remaining Text', 'Remaining Text'),
            //array('<?php ... __halt_compiler();' . "\0", "\0"),
            //array('<?php ... __halt_compiler /* */ ( ) ;Remaining Text', 'Remaining Text'),
        );
    }
}