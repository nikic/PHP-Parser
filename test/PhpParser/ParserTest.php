<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;

abstract class ParserTest extends TestCase
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

    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Unterminated comment on line 1
     */
    public function testParserThrowsLexerError() {
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php /*');
    }

    public function testAttributeAssignment() {
        $lexer = new Lexer([
            'usedAttributes' => [
                'comments', 'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ]
        ]);

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
        $this->assertEquals([
            'comments' => [
                new Comment\Doc('/** Doc comment */', 2, 6),
            ],
            'startLine' => 3,
            'endLine' => 7,
            'startTokenPos' => 3,
            'endTokenPos' => 21,
        ], $fn->getAttributes());

        $param = $fn->params[0];
        $this->assertInstanceOf('PhpParser\Node\Param', $param);
        $this->assertEquals([
            'startLine' => 3,
            'endLine' => 3,
            'startTokenPos' => 7,
            'endTokenPos' => 7,
        ], $param->getAttributes());

        /** @var \PhpParser\Node\Stmt\Echo_ $echo */
        $echo = $fn->stmts[0];
        $this->assertInstanceOf('PhpParser\Node\Stmt\Echo_', $echo);
        $this->assertEquals([
            'comments' => [
                new Comment("// Line\n", 4, 49),
                new Comment("// Comments\n", 5, 61),
            ],
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 16,
            'endTokenPos' => 19,
        ], $echo->getAttributes());

        /** @var \PhpParser\Node\Expr\Variable $var */
        $var = $echo->exprs[0];
        $this->assertInstanceOf('PhpParser\Node\Expr\Variable', $var);
        $this->assertEquals([
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 18,
            'endTokenPos' => 18,
        ], $var->getAttributes());
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
        $node = $stmts[0] instanceof Node\Stmt\Expression ? $stmts[0]->expr : $stmts[0];
        $attributes = $node->getAttributes();
        foreach ($expectedAttributes as $name => $value) {
            $this->assertSame($value, $attributes[$name]);
        }
    }

    public function provideTestExtraAttributes() {
        return [
            ['0', ['kind' => Scalar\LNumber::KIND_DEC]],
            ['9', ['kind' => Scalar\LNumber::KIND_DEC]],
            ['07', ['kind' => Scalar\LNumber::KIND_OCT]],
            ['0xf', ['kind' => Scalar\LNumber::KIND_HEX]],
            ['0XF', ['kind' => Scalar\LNumber::KIND_HEX]],
            ['0b1', ['kind' => Scalar\LNumber::KIND_BIN]],
            ['0B1', ['kind' => Scalar\LNumber::KIND_BIN]],
            ['[]', ['kind' => Expr\Array_::KIND_SHORT]],
            ['array()', ['kind' => Expr\Array_::KIND_LONG]],
            ["'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]],
            ["b'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]],
            ["B'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]],
            ['"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ['b"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ['B"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ['"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ['b"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ['B"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]],
            ["<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']],
            ["<<<STR\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["<<<\"STR\"\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["b<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']],
            ["B<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']],
            ["<<< \t 'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR']],
            // HHVM doesn't support this due to a lexer bug
            // (https://github.com/facebook/hhvm/issues/6970)
            // array("<<<'\xff'\n\xff\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => "\xff"]),
            ["<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["b<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["B<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["<<< \t \"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR']],
            ["die", ['kind' => Expr\Exit_::KIND_DIE]],
            ["die('done')", ['kind' => Expr\Exit_::KIND_DIE]],
            ["exit", ['kind' => Expr\Exit_::KIND_EXIT]],
            ["exit(1)", ['kind' => Expr\Exit_::KIND_EXIT]],
            ["?>Foo", ['hasLeadingNewline' => false]],
            ["?>\nFoo", ['hasLeadingNewline' => true]],
        ];
    }
}

class InvalidTokenLexer extends Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) : int {
        $value = 'foobar';
        return 999;
    }
}
