<?php declare(strict_types=1);

namespace PhpParser;

require __DIR__ . '/../../lib/PhpParser/compatibility_tokens.php';

class LexerTest extends \PHPUnit\Framework\TestCase {
    /* To allow overwriting in parent class */
    protected function getLexer() {
        return new Lexer();
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $messages): void {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not throw warnings from token_get_all()');
        }

        $errorHandler = new ErrorHandler\Collecting();
        $lexer = $this->getLexer();
        $lexer->tokenize($code, $errorHandler);
        $errors = $errorHandler->getErrors();

        $this->assertCount(count($messages), $errors);
        for ($i = 0; $i < count($messages); $i++) {
            $this->assertSame($messages[$i], $errors[$i]->getMessageWithColumnInfo($code));
        }
    }

    public static function provideTestError() {
        return [
            ["<?php /*", ["Unterminated comment from 1:7 to 1:9"]],
            ["<?php /*\n", ["Unterminated comment from 1:7 to 2:1"]],
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

    public function testDefaultErrorHandler(): void {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Unterminated comment on line 1');
        $lexer = $this->getLexer();
        $lexer->tokenize("<?php readonly /*");
    }

    /**
     * @dataProvider provideTestLex
     */
    public function testLex($code, $expectedTokens): void {
        $lexer = $this->getLexer();
        $tokens = $lexer->tokenize($code);
        foreach ($tokens as $token) {
            if ($token->id === 0 || $token->isIgnorable()) {
                continue;
            }

            $expectedToken = array_shift($expectedTokens);

            $this->assertSame($expectedToken[0], $token->id);
            $this->assertSame($expectedToken[1], $token->text);
        }
    }

    public static function provideTestLex() {
        return [
            // tests PHP 8 T_NAME_* emulation
            [
                '<?php Foo\Bar \Foo\Bar namespace\Foo\Bar Foo\Bar\\',
                [
                    [\T_NAME_QUALIFIED, 'Foo\Bar'],
                    [\T_NAME_FULLY_QUALIFIED, '\Foo\Bar'],
                    [\T_NAME_RELATIVE, 'namespace\Foo\Bar'],
                    [\T_NAME_QUALIFIED, 'Foo\Bar'],
                    [\T_NS_SEPARATOR, '\\'],
                ]
            ],
            // tests PHP 8 T_NAME_* emulation with reserved keywords
            [
                '<?php fn\use \fn\use namespace\fn\use fn\use\\',
                [
                    [\T_NAME_QUALIFIED, 'fn\use'],
                    [\T_NAME_FULLY_QUALIFIED, '\fn\use'],
                    [\T_NAME_RELATIVE, 'namespace\fn\use'],
                    [\T_NAME_QUALIFIED, 'fn\use'],
                    [\T_NS_SEPARATOR, '\\'],
                ]
            ],
        ];
    }

    public function testGetTokens(): void {
        $code = '<?php "a";' . "\n" . '// foo' . "\n" . '// bar' . "\n\n" . '"b";';
        $expectedTokens = [
            new Token(T_OPEN_TAG, '<?php ', 1, 0),
            new Token(T_CONSTANT_ENCAPSED_STRING, '"a"', 1, 6),
            new Token(\ord(';'), ';', 1, 9),
            new Token(T_WHITESPACE, "\n", 1, 10),
            new Token(T_COMMENT, '// foo', 2, 11),
            new Token(T_WHITESPACE, "\n", 2, 17),
            new Token(T_COMMENT, '// bar', 3, 18),
            new Token(T_WHITESPACE, "\n\n", 3, 24),
            new Token(T_CONSTANT_ENCAPSED_STRING, '"b"', 5, 26),
            new Token(\ord(';'), ';', 5, 29),
            new Token(0, "\0", 5, 30),
        ];

        $lexer = $this->getLexer();
        $this->assertEquals($expectedTokens, $lexer->tokenize($code));
    }
}
