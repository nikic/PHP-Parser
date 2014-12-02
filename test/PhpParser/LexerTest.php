<?php

namespace PhpParser;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /* To allow overwriting in parent class */
    protected function getLexer(array $options = array()) {
        return new Lexer($options);
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $message) {
        $lexer = $this->getLexer();
        try {
            $lexer->startLexing($code);
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
    public function testLex($code, $options, $tokens) {
        $lexer = $this->getLexer($options);
        $lexer->startLexing($code);
        while ($id = $lexer->getNextToken($value, $startAttributes, $endAttributes)) {
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
                array(),
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
                array(),
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
                array(),
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
                array(),
                array(
                    array(
                        Parser::T_CONSTANT_ENCAPSED_STRING, '"foo' . "\n" . 'bar"',
                        array('startLine' => 1), array('endLine' => 2)
                    ),
                )
            ),
            // tests exact file offsets
            array(
                '<?php "a";' . "\n" . '// foo' . "\n" . '"b";',
                array('usedAttributes' => array('startFilePos', 'endFilePos')),
                array(
                    array(
                        Parser::T_CONSTANT_ENCAPSED_STRING, '"a"',
                        array('startFilePos' => 6), array('endFilePos' => 9)
                    ),
                    array(
                        ord(';'), ';',
                        array('startFilePos' => 9), array('endFilePos' => 10)
                    ),
                    array(
                        Parser::T_CONSTANT_ENCAPSED_STRING, '"b"',
                        array('startFilePos' => 18), array('endFilePos' => 21)
                    ),
                    array(
                        ord(';'), ';',
                        array('startFilePos' => 21), array('endFilePos' => 22)
                    ),
                )
            ),
            // tests all attributes being disabled
            array(
                '<?php /* foo */ $bar;',
                array('usedAttributes' => array()),
                array(
                    array(
                        Parser::T_VARIABLE, '$bar',
                        array(), array()
                    ),
                    array(
                        ord(';'), ';',
                        array(), array()
                    )
                )
            )
        );
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function testHandleHaltCompiler($code, $remaining) {
        $lexer = $this->getLexer();
        $lexer->startLexing($code);

        while (Parser::T_HALT_COMPILER !== $lexer->getNextToken());

        $this->assertSame($remaining, $lexer->handleHaltCompiler());
        $this->assertSame(0, $lexer->getNextToken());
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
