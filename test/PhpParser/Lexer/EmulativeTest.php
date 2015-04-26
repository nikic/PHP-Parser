<?php

namespace PhpParser\Lexer;

use PhpParser\LexerTest;
use PhpParser\Parser;

require_once __DIR__ . '/../LexerTest.php';

class EmulativeTest extends LexerTest
{
    protected function getLexer(array $options = array()) {
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

        $this->assertSame(Parser::T_OBJECT_OPERATOR, $lexer->getNextToken());
        $this->assertSame(Parser::T_STRING, $lexer->getNextToken());
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestReplaceKeywords() {
        return array(
            // PHP 5.5
            array('finally',       Parser::T_FINALLY),
            array('yield',         Parser::T_YIELD),

            // PHP 5.4
            array('callable',      Parser::T_CALLABLE),
            array('insteadof',     Parser::T_INSTEADOF),
            array('trait',         Parser::T_TRAIT),
            array('__TRAIT__',     Parser::T_TRAIT_C),

            // PHP 5.3
            array('__DIR__',       Parser::T_DIR),
            array('goto',          Parser::T_GOTO),
            array('namespace',     Parser::T_NAMESPACE),
            array('__NAMESPACE__', Parser::T_NS_C),
        );
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

        $this->assertSame(Parser::T_CONSTANT_ENCAPSED_STRING, $lexer->getNextToken($text));
        $this->assertSame($stringifiedToken, $text);
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestLexNewFeatures() {
        return array(
            array('yield from', array(
                array(Parser::T_YIELD_FROM, 'yield from'),
            )),
            array("yield\r\nfrom", array(
                array(Parser::T_YIELD_FROM, "yield\r\nfrom"),
            )),
            array('...', array(
                array(Parser::T_ELLIPSIS, '...'),
            )),
            array('**', array(
                array(Parser::T_POW, '**'),
            )),
            array('**=', array(
                array(Parser::T_POW_EQUAL, '**='),
            )),
            array('??', array(
                array(Parser::T_COALESCE, '??'),
            )),
            array('<=>', array(
                array(Parser::T_SPACESHIP, '<=>'),
            )),
            array('0b1010110', array(
                array(Parser::T_LNUMBER, '0b1010110'),
            )),
            array('0b1011010101001010110101010010101011010101010101101011001110111100', array(
                array(Parser::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'),
            )),
            array('\\', array(
                array(Parser::T_NS_SEPARATOR, '\\'),
            )),
            array("<<<'NOWDOC'\nNOWDOC;\n", array(
                array(Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
            array("<<<'NOWDOC'\nFoobar\nNOWDOC;\n", array(
                array(Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(Parser::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"),
                array(Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
        );
    }
}
