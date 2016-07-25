<?php

namespace PhpParser;

use PhpParser\Comment;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;

abstract class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @returns Parser */
    abstract protected function getParser(Lexer $lexer);

    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Syntax error, unexpected EOF on line 1
     */
    public function testParserThrowsSyntaxError() {
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php foo');
    }

    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Cannot use foo as self because 'self' is a special class name on line 1
     */
    public function testParserThrowsSpecialError() {
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php use foo as self;');
    }

    public function testAttributeAssignment() {
        $lexer = new Lexer(array(
            'usedAttributes' => array(
                'comments', 'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            )
        ));

        $code = <<<'EOC'
<?php
/** Doc comment */
function test($a) {
    // Line
    // Comments
    echo $a;
}
EOC;
        $code = canonicalize($code);

        $parser = $this->getParser($lexer);
        $stmts = $parser->parse($code);

        /** @var \PhpParser\Node\Stmt\Function_ $fn */
        $fn = $stmts[0];
        $this->assertInstanceOf('PhpParser\Node\Stmt\Function_', $fn);
        $this->assertEquals(array(
            'comments' => array(
                new Comment\Doc('/** Doc comment */', 2, 6),
            ),
            'startLine' => 3,
            'endLine' => 7,
            'startTokenPos' => 3,
            'endTokenPos' => 21,
        ), $fn->getAttributes());

        $param = $fn->params[0];
        $this->assertInstanceOf('PhpParser\Node\Param', $param);
        $this->assertEquals(array(
            'startLine' => 3,
            'endLine' => 3,
            'startTokenPos' => 7,
            'endTokenPos' => 7,
        ), $param->getAttributes());

        /** @var \PhpParser\Node\Stmt\Echo_ $echo */
        $echo = $fn->stmts[0];
        $this->assertInstanceOf('PhpParser\Node\Stmt\Echo_', $echo);
        $this->assertEquals(array(
            'comments' => array(
                new Comment("// Line\n", 4, 49),
                new Comment("// Comments\n", 5, 61),
            ),
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 16,
            'endTokenPos' => 19,
        ), $echo->getAttributes());

        /** @var \PhpParser\Node\Expr\Variable $var */
        $var = $echo->exprs[0];
        $this->assertInstanceOf('PhpParser\Node\Expr\Variable', $var);
        $this->assertEquals(array(
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 18,
            'endTokenPos' => 18,
        ), $var->getAttributes());
    }

    /**
     * @expectedException \RangeException
     * @expectedExceptionMessage The lexer returned an invalid token (id=999, value=foobar)
     */
    public function testInvalidToken() {
        $lexer = new InvalidTokenLexer;
        $parser = $this->getParser($lexer);
        $parser->parse('dummy');
    }

    /**
     * @dataProvider provideTestExtraAttributes
     */
    public function testExtraAttributes($code, $expectedAttributes) {
        $parser = $this->getParser(new Lexer);
        $stmts = $parser->parse("<?php $code;");
        $attributes = $stmts[0]->getAttributes();
        foreach ($expectedAttributes as $name => $value) {
            $this->assertSame($value, $attributes[$name]);
        }
    }

    public function provideTestExtraAttributes() {
        return array(
            array('0', ['kind' => Scalar\LNumber::KIND_DEC]),
            array('9', ['kind' => Scalar\LNumber::KIND_DEC]),
            array('07', ['kind' => Scalar\LNumber::KIND_OCT]),
            array('0xf', ['kind' => Scalar\LNumber::KIND_HEX]),
            array('0XF', ['kind' => Scalar\LNumber::KIND_HEX]),
            array('0b1', ['kind' => Scalar\LNumber::KIND_BIN]),
            array('0B1', ['kind' => Scalar\LNumber::KIND_BIN]),
            array('[]', ['kind' => Expr\Array_::KIND_SHORT]),
            array('array()', ['kind' => Expr\Array_::KIND_LONG]),
            array("'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]),
            array("b'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]),
            array("B'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]),
            array('"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array('b"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array('B"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array('"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array('b"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array('B"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]),
            array("<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']),
            array("<<<STR\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("<<<\"STR\"\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("b<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']),
            array("B<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']),
            array("<<< \t 'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']),
            // HHVM doesn't support this due to a lexer bug
            // (https://github.com/facebook/hhvm/issues/6970)
            // array("<<<'\xff'\n\xff\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => "\xff"]),
            array("<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("b<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("B<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("<<< \t \"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']),
            array("die", ['kind' => Expr\Exit_::KIND_DIE]),
            array("die('done')", ['kind' => Expr\Exit_::KIND_DIE]),
            array("exit", ['kind' => Expr\Exit_::KIND_EXIT]),
            array("exit(1)", ['kind' => Expr\Exit_::KIND_EXIT]),
            array("?>Foo", ['hasLeadingNewline' => false]),
            array("?>\nFoo", ['hasLeadingNewline' => true]),
        );
    }
}

class InvalidTokenLexer extends Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $value = 'foobar';
        return 999;
    }
}
