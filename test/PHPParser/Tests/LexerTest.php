<?php

class PHPParser_Tests_LexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $message) {
        try {
            new PHPParser_Lexer($code);
        } catch (PHPParser_Error $e) {
            $this->assertEquals($message, $e->getMessage());

            return;
        }

        $this->fail('Expected PHPParser_Error');
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
        $lexer = new PHPParser_Lexer($code);

        while ($id = $lexer->lex($value, $line, $docComment)) {
            $token = array_shift($tokens);

            $this->assertEquals($token[0], $id);
            $this->assertEquals($token[1], $value);
            $this->assertEquals($token[2], $line);
            $this->assertEquals($token[3], $docComment);
        }
    }

    public function provideTestLex() {
        return array(
            // tests conversion of closing PHP tag and drop of whitespace, comments and opening tags
            array(
                '<?php tokens // ?>plaintext',
                array(
                    array(PHPParser_Parser::T_STRING,      'tokens',    1, null),
                    array(ord(';'),                        '?>',        1, null),
                    array(PHPParser_Parser::T_INLINE_HTML, 'plaintext', 1, null),
                )
            ),
            // tests line numbers
            array(
                '<?php' . "\n" . '$ token /** doc' . "\n" . 'comment */ $',
                array(
                    array(ord('$'),                   '$',     2, null),
                    array(PHPParser_Parser::T_STRING, 'token', 2, null),
                    array(ord('$'),                   '$',     3, '/** doc' . "\n" . 'comment */')
                )
            ),
            // tests doccomment extraction
            array(
                '<?php /** docComment 1 *//** docComment 2 */ token',
                array(
                    array(PHPParser_Parser::T_STRING, 'token', 1, '/** docComment 2 */'),
                )
            ),
        );
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function testHandleHaltCompiler($code, $remaining) {
        $lexer = new PHPParser_Lexer($code);

        while (PHPParser_Parser::T_HALT_COMPILER !== $lexer->lex());

        $this->assertEquals($lexer->handleHaltCompiler(), $remaining);
        $this->assertEquals(0, $lexer->lex());
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