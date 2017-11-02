<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\LexerTest;
use PhpParser\Parser\Tokens;

require_once __DIR__ . '/../LexerTest.php';

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

        foreach ($expectedTokens as $expectedToken) {
            list($expectedTokenType, $expectedTokenText) = $expectedToken;
            $this->assertSame($expectedTokenType, $lexer->getNextToken($text));
            $this->assertSame($expectedTokenText, $text);
        }
        $this->assertSame(0, $lexer->getNextToken());
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

    public function provideTestLexNewFeatures() {
        return [
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
        ];
    }
}
