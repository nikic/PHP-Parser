<?php

class PHPParser_Tests_Lexer_EmulativeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testReplaceKeywords($keyword, $expectedToken) {
        $lexer = new PHPParser_Lexer_Emulative('<?php ' . $keyword);

        $this->assertEquals($expectedToken, $lexer->lex());
        $this->assertEquals(0, $lexer->lex());
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testNoReplaceKeywordsAfterObjectOperator($keyword) {
        $lexer = new PHPParser_Lexer_Emulative('<?php ->' . $keyword);

        $this->assertEquals(PHPParser_Parser::T_OBJECT_OPERATOR, $lexer->lex());
        $this->assertEquals(PHPParser_Parser::T_STRING, $lexer->lex());
        $this->assertEquals(0, $lexer->lex());
    }

    public function provideTestReplaceKeywords() {
        return array(
            array('callable',      PHPParser_Parser::T_CALLABLE),
            array('insteadof',     PHPParser_Parser::T_INSTEADOF),
            array('trait',         PHPParser_Parser::T_TRAIT),
            array('__TRAIT__',     PHPParser_Parser::T_TRAIT_C),
            array('__DIR__',       PHPParser_Parser::T_DIR),
            array('goto',          PHPParser_Parser::T_GOTO),
            array('namespace',     PHPParser_Parser::T_NAMESPACE),
            array('__NAMESPACE__', PHPParser_Parser::T_NS_C),
        );
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLexNewFeatures($code, array $expectedTokens) {
        $lexer = new PHPParser_Lexer_Emulative('<?php ' . $code);

        foreach ($expectedTokens as $expectedToken) {
            list($expectedTokenType, $expectedTokenText) = $expectedToken;
            $this->assertEquals($expectedTokenType, $lexer->lex($text));
            $this->assertEquals($expectedTokenText, $text);
        }
        $this->assertEquals(0, $lexer->lex());
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLeaveStuffAloneInStrings($code) {
        $stringifiedToken = '"' . addcslashes($code, '"\\') . '"';
        $lexer = new PHPParser_Lexer_Emulative('<?php ' . $stringifiedToken);

        $this->assertEquals(PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING, $lexer->lex($text));
        $this->assertEquals($stringifiedToken, $text);
        $this->assertEquals(0, $lexer->lex());
    }

    public function provideTestLexNewFeatures() {
        return array(
            array('0b1010110', array(
                array(PHPParser_Parser::T_LNUMBER, '0b1010110'),
            )),
            array('0b10110101010010101101010100101010110101010101011010110', array(
                array(PHPParser_Parser::T_DNUMBER, '0b10110101010010101101010100101010110101010101011010110'),
            )),
            array('\\', array(
                array(PHPParser_Parser::T_NS_SEPARATOR, '\\'),
            )),
            array("<<<'NOWDOC'\nNOWDOC;\n", array(
                array(PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
            array("<<<'NOWDOC'\nFoobar\nNOWDOC;\n", array(
                array(PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"),
                array(PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
        );
    }
}