<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\ErrorHandler;
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

    public function provideTestReplaceKeywords(): \Iterator
    {
        // PHP 5.5
        yield ['finally',       Tokens::T_FINALLY];
        yield ['yield',         Tokens::T_YIELD];
        // PHP 5.4
        yield ['callable',      Tokens::T_CALLABLE];
        yield ['insteadof',     Tokens::T_INSTEADOF];
        yield ['trait',         Tokens::T_TRAIT];
        yield ['__TRAIT__',     Tokens::T_TRAIT_C];
        // PHP 5.3
        yield ['__DIR__',       Tokens::T_DIR];
        yield ['goto',          Tokens::T_GOTO];
        yield ['namespace',     Tokens::T_NAMESPACE];
        yield ['__NAMESPACE__', Tokens::T_NS_C];
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

    public function provideTestLexNewFeatures(): \Iterator
    {
        yield ['yield from', [
            [Tokens::T_YIELD_FROM, 'yield from'],
        ]];
        yield ["yield\r\nfrom", [
            [Tokens::T_YIELD_FROM, "yield\r\nfrom"],
        ]];
        yield ['...', [
            [Tokens::T_ELLIPSIS, '...'],
        ]];
        yield ['**', [
            [Tokens::T_POW, '**'],
        ]];
        yield ['**=', [
            [Tokens::T_POW_EQUAL, '**='],
        ]];
        yield ['??', [
            [Tokens::T_COALESCE, '??'],
        ]];
        yield ['<=>', [
            [Tokens::T_SPACESHIP, '<=>'],
        ]];
        yield ['0b1010110', [
            [Tokens::T_LNUMBER, '0b1010110'],
        ]];
        yield ['0b1011010101001010110101010010101011010101010101101011001110111100', [
            [Tokens::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'],
        ]];
        yield ['\\', [
            [Tokens::T_NS_SEPARATOR, '\\'],
        ]];
        yield ["<<<'NOWDOC'\nNOWDOC;\n", [
            [Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"],
            [Tokens::T_END_HEREDOC, 'NOWDOC'],
            [ord(';'), ';'],
        ]];
        yield ["<<<'NOWDOC'\nFoobar\nNOWDOC;\n", [
            [Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"],
            [Tokens::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"],
            [Tokens::T_END_HEREDOC, 'NOWDOC'],
            [ord(';'), ';'],
        ]];
        // Flexible heredoc/nowdoc
        yield ["<<<LABEL\nLABEL,", [
            [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
            [Tokens::T_END_HEREDOC, "LABEL"],
            [ord(','), ','],
        ]];
        yield ["<<<LABEL\n    LABEL,", [
            [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
            [Tokens::T_END_HEREDOC, "    LABEL"],
            [ord(','), ','],
        ]];
        yield ["<<<LABEL\n    Foo\n  LABEL;", [
            [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
            [Tokens::T_ENCAPSED_AND_WHITESPACE, "    Foo\n"],
            [Tokens::T_END_HEREDOC, "  LABEL"],
            [ord(';'), ';'],
        ]];
        yield ["<<<A\n A,<<<A\n A,", [
            [Tokens::T_START_HEREDOC, "<<<A\n"],
            [Tokens::T_END_HEREDOC, " A"],
            [ord(','), ','],
            [Tokens::T_START_HEREDOC, "<<<A\n"],
            [Tokens::T_END_HEREDOC, " A"],
            [ord(','), ','],
        ]];
        yield ["<<<LABEL\nLABELNOPE\nLABEL\n", [
            [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
            [Tokens::T_ENCAPSED_AND_WHITESPACE, "LABELNOPE\n"],
            [Tokens::T_END_HEREDOC, "LABEL"],
        ]];
        // Interpretation changed
        yield ["<<<LABEL\n    LABEL\nLABEL\n", [
            [Tokens::T_START_HEREDOC, "<<<LABEL\n"],
            [Tokens::T_END_HEREDOC, "    LABEL"],
            [Tokens::T_STRING, "LABEL"],
        ]];
    }
}
