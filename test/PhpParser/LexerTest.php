<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Tokens;

class LexerTest extends \PHPUnit\Framework\TestCase
{
    /* To allow overwriting in parent class */
    protected function getLexer(array $options = []) {
        return new Lexer($options);
    }

    public function testTokenize() {
        $code = '<?php "a";' . "\n" . '// foo' . "\n" . '"b";';
        $expectedTokens = [
            new Token(Tokens::T_OPEN_TAG, '<?php ', 1, 0),
            new Token(Tokens::T_CONSTANT_ENCAPSED_STRING, '"a"', 1, 6),
            new Token(\ord(';'), ';', 1, 9),
            new Token(Tokens::T_WHITESPACE, "\n", 1, 10),
            new Token(Tokens::T_COMMENT, '// foo' . "\n", 2, 11),
            new Token(Tokens::T_CONSTANT_ENCAPSED_STRING, '"b"', 3, 18),
            new Token(\ord(';'), ';', 3, 21),
            new Token(0, "\0", 3, 22),
        ];

        $lexer = $this->getLexer();
        $this->assertEquals($expectedTokens, $lexer->tokenize($code));
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $messages) {
        $errorHandler = new ErrorHandler\Collecting();
        $lexer = $this->getLexer();
        $lexer->tokenize($code, $errorHandler);
        $errors = $errorHandler->getErrors();

        $this->assertCount(count($messages), $errors);
        for ($i = 0; $i < count($messages); $i++) {
            $this->assertSame($messages[$i], $errors[$i]->getMessageWithColumnInfo($code));
        }
    }

    public function provideTestError() {
        return [
            ["<?php /*", ["Unterminated comment from 1:7 to 1:9"]],
            ["<?php \1", ["Unexpected character \"\1\" (ASCII 1) from 1:7 to 1:7"]],
            ["<?php \0", ["Unexpected null byte from 1:7 to 1:7"]],
            // Error with potentially emulated token
            ["<?php ?? \0", ["Unexpected null byte from 1:10 to 1:10"]],
            ["<?php\n\0\1 foo /* bar", [
                "Unexpected null byte from 2:1 to 2:1",
                "Unexpected character \"\1\" (ASCII 1) from 2:2 to 2:2",
                "Unterminated comment from 2:8 to 2:14"
            ]],
        ];
    }
}
