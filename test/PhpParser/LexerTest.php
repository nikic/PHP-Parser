<?php declare(strict_types=1);

namespace PhpParser;

require __DIR__ . '/../../lib/PhpParser/compatibility_tokens.php';

class LexerTest extends \PHPUnit\Framework\TestCase {
    /* To allow overwriting in parent class */
    protected function getLexer(array $options = []) {
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

        $this->assertCount(count($messages), $errors);
        for ($i = 0; $i < count($messages); $i++) {
            $this->assertSame($messages[$i], $errors[$i]->getMessageWithColumnInfo($code));
        }
    }

    public function provideTestError() {
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
        return [
            // tests conversion of closing PHP tag and drop of whitespace and opening tags
            [
                '<?php tokens ?>plaintext',
                [],
                [
                    [
                        \T_STRING, 'tokens',
                        ['startLine' => 1], ['endLine' => 1]
                    ],
                    [
                        \T_CLOSE_TAG, '?>',
                        ['startLine' => 1], ['endLine' => 1]
                    ],
                    [
                        \T_INLINE_HTML, 'plaintext',
                        ['startLine' => 1, 'hasLeadingNewline' => false],
                        ['endLine' => 1]
                    ],
                ]
            ],
            // tests line numbers
            [
                '<?php' . "\n" . '$ token /** doc' . "\n" . 'comment */ $',
                [],
                [
                    [
                        ord('$'), '$',
                        ['startLine' => 2], ['endLine' => 2]
                    ],
                    [
                        \T_STRING, 'token',
                        ['startLine' => 2], ['endLine' => 2]
                    ],
                    [
                        ord('$'), '$',
                        [
                            'startLine' => 3,
                            'comments' => [
                                new Comment\Doc('/** doc' . "\n" . 'comment */',
                                    2, 14, 5,
                                    3, 31, 5),
                            ]
                        ],
                        ['endLine' => 3]
                    ],
                ]
            ],
            // tests comment extraction
            [
                '<?php /* comment */ // comment' . "\n" . '/** docComment 1 *//** docComment 2 */ token',
                [],
                [
                    [
                        \T_STRING, 'token',
                        [
                            'startLine' => 2,
                            'comments' => [
                                new Comment('/* comment */',
                                    1, 6, 1, 1, 18, 1),
                                new Comment('// comment',
                                    1, 20, 3, 1, 29, 3),
                                new Comment\Doc('/** docComment 1 */',
                                    2, 31, 5, 2, 49, 5),
                                new Comment\Doc('/** docComment 2 */',
                                    2, 50, 6, 2, 68, 6),
                            ],
                        ],
                        ['endLine' => 2]
                    ],
                ]
            ],
            // tests differing start and end line
            [
                '<?php "foo' . "\n" . 'bar"',
                [],
                [
                    [
                        \T_CONSTANT_ENCAPSED_STRING, '"foo' . "\n" . 'bar"',
                        ['startLine' => 1], ['endLine' => 2]
                    ],
                ]
            ],
            // tests exact file offsets
            [
                '<?php "a";' . "\n" . '// foo' . "\n" . '"b";',
                ['usedAttributes' => ['startFilePos', 'endFilePos']],
                [
                    [
                        \T_CONSTANT_ENCAPSED_STRING, '"a"',
                        ['startFilePos' => 6], ['endFilePos' => 8]
                    ],
                    [
                        ord(';'), ';',
                        ['startFilePos' => 9], ['endFilePos' => 9]
                    ],
                    [
                        \T_CONSTANT_ENCAPSED_STRING, '"b"',
                        ['startFilePos' => 18], ['endFilePos' => 20]
                    ],
                    [
                        ord(';'), ';',
                        ['startFilePos' => 21], ['endFilePos' => 21]
                    ],
                ]
            ],
            // tests token offsets
            [
                '<?php "a";' . "\n" . '// foo' . "\n" . '"b";',
                ['usedAttributes' => ['startTokenPos', 'endTokenPos']],
                [
                    [
                        \T_CONSTANT_ENCAPSED_STRING, '"a"',
                        ['startTokenPos' => 1], ['endTokenPos' => 1]
                    ],
                    [
                        ord(';'), ';',
                        ['startTokenPos' => 2], ['endTokenPos' => 2]
                    ],
                    [
                        \T_CONSTANT_ENCAPSED_STRING, '"b"',
                        ['startTokenPos' => 6], ['endTokenPos' => 6]
                    ],
                    [
                        ord(';'), ';',
                        ['startTokenPos' => 7], ['endTokenPos' => 7]
                    ],
                ]
            ],
            // tests all attributes being disabled
            [
                '<?php /* foo */ $bar;',
                ['usedAttributes' => []],
                [
                    [
                        \T_VARIABLE, '$bar',
                        [], []
                    ],
                    [
                        ord(';'), ';',
                        [], []
                    ]
                ]
            ],
            // tests no tokens
            [
                '',
                [],
                []
            ],
            // tests PHP 8 T_NAME_* emulation
            [
                '<?php Foo\Bar \Foo\Bar namespace\Foo\Bar Foo\Bar\\',
                ['usedAttributes' => []],
                [
                    [\T_NAME_QUALIFIED, 'Foo\Bar', [], []],
                    [\T_NAME_FULLY_QUALIFIED, '\Foo\Bar', [], []],
                    [\T_NAME_RELATIVE, 'namespace\Foo\Bar', [], []],
                    [\T_NAME_QUALIFIED, 'Foo\Bar', [], []],
                    [\T_NS_SEPARATOR, '\\', [], []],
                ]
            ],
            // tests PHP 8 T_NAME_* emulation with reserved keywords
            [
                '<?php fn\use \fn\use namespace\fn\use fn\use\\',
                ['usedAttributes' => []],
                [
                    [\T_NAME_QUALIFIED, 'fn\use', [], []],
                    [\T_NAME_FULLY_QUALIFIED, '\fn\use', [], []],
                    [\T_NAME_RELATIVE, 'namespace\fn\use', [], []],
                    [\T_NAME_QUALIFIED, 'fn\use', [], []],
                    [\T_NS_SEPARATOR, '\\', [], []],
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function testHandleHaltCompiler($code, $remaining) {
        $lexer = $this->getLexer();
        $lexer->startLexing($code);

        while (\T_HALT_COMPILER !== $lexer->getNextToken());
        $lexer->getNextToken();
        $lexer->getNextToken();
        $lexer->getNextToken();

        $this->assertSame($remaining, $lexer->handleHaltCompiler());
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestHaltCompiler() {
        return [
            ['<?php ... __halt_compiler();', ''],
            ['<?php ... __halt_compiler();Remaining Text', 'Remaining Text'],
            ['<?php ... __halt_compiler ( ) ;Remaining Text', 'Remaining Text'],
            ['<?php ... __halt_compiler() ?>Remaining Text', 'Remaining Text'],
            ['<?php ... __halt_compiler();' . "\0", "\0"],
            ['<?php ... __halt_compiler /* */ ( ) ;Remaining Text', 'Remaining Text'],
        ];
    }

    public function testGetTokens() {
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
        $lexer->startLexing($code);
        $this->assertEquals($expectedTokens, $lexer->getTokens());
    }
}
