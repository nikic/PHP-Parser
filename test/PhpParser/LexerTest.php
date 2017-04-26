<?php

namespace PhpParser;

use PhpParser\Parser\Tokens;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /* To allow overwriting in parent class */
    protected function getLexer(array $options = array()) {
        return new Lexer($options);
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $messages) {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not throw warnings from token_get_all()');
        }

        $errorHandler = new ErrorHandler\Collecting();
        $lexer = $this->getLexer(['usedAttributes' => [
            'comments', 'startLine', 'endLine', 'startFilePos', 'endFilePos'
        ]]);
        $lexer->startLexing($code, $errorHandler);
        $errors = $errorHandler->getErrors();

        $this->assertSame(count($messages), count($errors));
        for ($i = 0; $i < count($messages); $i++) {
            $this->assertSame($messages[$i], $errors[$i]->getMessageWithColumnInfo($code));
        }
    }

    public function provideTestError() {
        return array(
            array("<?php /*", array("Unterminated comment from 1:7 to 1:9")),
            array("<?php \1", array("Unexpected character \"\1\" (ASCII 1) from 1:7 to 1:7")),
            array("<?php \0", array("Unexpected null byte from 1:7 to 1:7")),
            // Error with potentially emulated token
            array("<?php ?? \0", array("Unexpected null byte from 1:10 to 1:10")),
            array("<?php\n\0\1 foo /* bar", array(
                "Unexpected null byte from 2:1 to 2:1",
                "Unexpected character \"\1\" (ASCII 1) from 2:2 to 2:2",
                "Unterminated comment from 2:8 to 2:14"
            )),
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
                        Tokens::T_STRING, 'tokens',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        ord(';'), '?>',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        Tokens::T_INLINE_HTML, 'plaintext',
                        array('startLine' => 1, 'hasLeadingNewline' => false),
                        array('endLine' => 1)
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
                        Tokens::T_STRING, 'token',
                        array('startLine' => 2), array('endLine' => 2)
                    ),
                    array(
                        ord('$'), '$',
                        array(
                            'startLine' => 3,
                            'comments' => array(
                                new Comment\Doc('/** doc' . "\n" . 'comment */', 2, 14),
                            )
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
                        Tokens::T_STRING, 'token',
                        array(
                            'startLine' => 2,
                            'comments' => array(
                                new Comment('/* comment */', 1, 6),
                                new Comment('// comment' . "\n", 1, 20),
                                new Comment\Doc('/** docComment 1 */', 2, 31),
                                new Comment\Doc('/** docComment 2 */', 2, 50),
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
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"foo' . "\n" . 'bar"',
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
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"a"',
                        array('startFilePos' => 6), array('endFilePos' => 8)
                    ),
                    array(
                        ord(';'), ';',
                        array('startFilePos' => 9), array('endFilePos' => 9)
                    ),
                    array(
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"b"',
                        array('startFilePos' => 18), array('endFilePos' => 20)
                    ),
                    array(
                        ord(';'), ';',
                        array('startFilePos' => 21), array('endFilePos' => 21)
                    ),
                )
            ),
            // tests token offsets
            array(
                '<?php "a";' . "\n" . '// foo' . "\n" . '"b";',
                array('usedAttributes' => array('startTokenPos', 'endTokenPos')),
                array(
                    array(
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"a"',
                        array('startTokenPos' => 1), array('endTokenPos' => 1)
                    ),
                    array(
                        ord(';'), ';',
                        array('startTokenPos' => 2), array('endTokenPos' => 2)
                    ),
                    array(
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"b"',
                        array('startTokenPos' => 5), array('endTokenPos' => 5)
                    ),
                    array(
                        ord(';'), ';',
                        array('startTokenPos' => 6), array('endTokenPos' => 6)
                    ),
                )
            ),
            // tests all attributes being disabled
            array(
                '<?php /* foo */ $bar;',
                array('usedAttributes' => array()),
                array(
                    array(
                        Tokens::T_VARIABLE, '$bar',
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

        while (Tokens::T_HALT_COMPILER !== $lexer->getNextToken());

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

    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage __HALT_COMPILER must be followed by "();"
     */
    public function testHandleHaltCompilerError() {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ... __halt_compiler invalid ();');

        while (Tokens::T_HALT_COMPILER !== $lexer->getNextToken());
        $lexer->handleHaltCompiler();
    }

    public function testGetTokens() {
        $code = '<?php "a";' . "\n" . '// foo' . "\n" . '"b";';
        $expectedTokens = array(
            array(T_OPEN_TAG, '<?php ', 1),
            array(T_CONSTANT_ENCAPSED_STRING, '"a"', 1),
            ';',
            array(T_WHITESPACE, "\n", 1),
            array(T_COMMENT, '// foo' . "\n", 2),
            array(T_CONSTANT_ENCAPSED_STRING, '"b"', 3),
            ';',
        );

        $lexer = $this->getLexer();
        $lexer->startLexing($code);
        $this->assertSame($expectedTokens, $lexer->getTokens());
    }
}
