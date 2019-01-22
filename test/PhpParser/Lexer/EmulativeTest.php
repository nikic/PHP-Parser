<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\LexerTest;
use PhpParser\Parser\Tokens;

class EmulativeTest extends LexerTest
{
    protected function getLexer(array $options = []) {
        return new Emulative($options);
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testReplaceKeywords($keyword, $expectedToken) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $keyword);

        $this->assertSame($expectedToken, $lexer->getNextToken());
        $this->assertSame(0, $lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testNoReplaceKeywordsAfterObjectOperator($keyword) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ->' . $keyword);

        $this->assertSame(Tokens::T_OBJECT_OPERATOR, $lexer->getNextToken());
        $this->assertSame(Tokens::T_STRING, $lexer->getNextToken());
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestReplaceKeywords() {
        return [
            // PHP 5.5
            ['finally',       Tokens::T_FINALLY],
            ['yield',         Tokens::T_YIELD],

            // PHP 5.4
            ['callable',      Tokens::T_CALLABLE],
            ['insteadof',     Tokens::T_INSTEADOF],
            ['trait',         Tokens::T_TRAIT],
            ['__TRAIT__',     Tokens::T_TRAIT_C],

            // PHP 5.3
            ['__DIR__',       Tokens::T_DIR],
            ['goto',          Tokens::T_GOTO],
            ['namespace',     Tokens::T_NAMESPACE],
            ['__NAMESPACE__', Tokens::T_NS_C],
        ];
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLexNewFeatures($code, array $expectedTokens) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $code);

        $tokens = [];
        while (0 !== $token = $lexer->getNextToken($text)) {
            $tokens[] = [$token, $text];
        }
        $this->assertSame($expectedTokens, $tokens);
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLeaveStuffAloneInStrings($code) {
        $stringifiedToken = '"' . addcslashes($code, '"\\') . '"';

        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $stringifiedToken);

        $this->assertSame(Tokens::T_CONSTANT_ENCAPSED_STRING, $lexer->getNextToken($text));
        $this->assertSame($stringifiedToken, $text);
        $this->assertSame(0, $lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testErrorAfterEmulation($code) {
        $errorHandler = new ErrorHandler\Collecting;
        $lexer = $this->getLexer([]);
        $lexer->startLexing('<?php ' . $code . "\0", $errorHandler);

        $errors = $errorHandler->getErrors();
        $this->assertCount(1, $errors);

        $error = $errors[0];
        $this->assertSame('Unexpected null byte', $error->getRawMessage());

        $attrs = $error->getAttributes();
        $expPos = strlen('<?php ' . $code);
        $expLine = 1 + substr_count('<?php ' . $code, "\n");
        $this->assertSame($expPos, $attrs['startFilePos']);
        $this->assertSame($expPos, $attrs['endFilePos']);
        $this->assertSame($expLine, $attrs['startLine']);
        $this->assertSame($expLine, $attrs['endLine']);
    }

    public function provideTestLexNewFeatures() {
        return [
            // PHP 7.4
            ['??=', [
                [Tokens::T_COALESCE_EQUAL, '??='],
            ]],
            ['yield from', [
                [Tokens::T_YIELD_FROM, 'yield from'],
            ]],
            ["yield\r\nfrom", [
                [Tokens::T_YIELD_FROM, "yield\r\nfrom"],
            ]],
            ['...', [
                [Tokens::T_ELLIPSIS, '...'],
            ]],
            ['**', [
                [Tokens::T_POW, '**'],
            ]],
            ['**=', [
                [Tokens::T_POW_EQUAL, '**='],
            ]],
            ['??', [
                [Tokens::T_COALESCE, '??'],
            ]],
            ['<=>', [
                [Tokens::T_SPACESHIP, '<=>'],
            ]],
            ['0b1010110', [
                [Tokens::T_LNUMBER, '0b1010110'],
            ]],
            ['0b1011010101001010110101010010101011010101010101101011001110111100', [
                [Tokens::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'],
            ]],
            ['\\', [
                [Tokens::T_NS_SEPARATOR, '\\'],
            ]],
            ["<<<'NOWDOC'\nNOWDOC;\n", [
                [Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"],
                [Tokens::T_END_HEREDOC, 'NOWDOC'],
                [ord(';'), ';'],
            ]],
            ["<<<'NOWDOC'\nFoobar\nNOWDOC;\n", [
                [Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"],
                [Tokens::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"],
                [Tokens::T_END_HEREDOC, 'NOWDOC'],
                [ord(';'), ';'],
            ]],

            // Flexible heredoc/nowdoc
            ["<<<LABEL\nLABEL,", [
                [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
                [Tokens::T_END_HEREDOC, "LABEL"],
                [ord(','), ','],
            ]],
            ["<<<LABEL\n    LABEL,", [
                [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
                [Tokens::T_END_HEREDOC, "    LABEL"],
                [ord(','), ','],
            ]],
            ["<<<LABEL\n    Foo\n  LABEL;", [
                [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
                [Tokens::T_ENCAPSED_AND_WHITESPACE, "    Foo\n"],
                [Tokens::T_END_HEREDOC, "  LABEL"],
                [ord(';'), ';'],
            ]],
            ["<<<A\n A,<<<A\n A,", [
                [Tokens::T_START_HEREDOC, "<<<A\n"],
                [Tokens::T_END_HEREDOC, " A"],
                [ord(','), ','],
                [Tokens::T_START_HEREDOC, "<<<A\n"],
                [Tokens::T_END_HEREDOC, " A"],
                [ord(','), ','],
            ]],
            ["<<<LABEL\nLABELNOPE\nLABEL\n", [
                [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
                [Tokens::T_ENCAPSED_AND_WHITESPACE, "LABELNOPE\n"],
                [Tokens::T_END_HEREDOC, "LABEL"],
            ]],
            // Interpretation changed
            ["<<<LABEL\n    LABEL\nLABEL\n", [
                [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
                [Tokens::T_END_HEREDOC, "    LABEL"],
                [Tokens::T_STRING, "LABEL"],
            ]],
        ];
    }
}
