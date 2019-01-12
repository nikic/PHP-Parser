<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;

abstract class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @returns Parser */
    abstract protected function getParser(Lexer $lexer);

    public function testParserThrowsSyntaxError() {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Syntax error, unexpected EOF on line 1');
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php foo');
    }

    public function testParserThrowsSpecialError() {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot use foo as self because \'self\' is a special class name on line 1');
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php use foo as self;');
    }

    public function testParserThrowsLexerError() {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Unterminated comment on line 1');
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

        /** @var Stmt\Function_ $fn */
        $fn = $stmts[0];
        $this->assertInstanceOf(Stmt\Function_::class, $fn);
        $this->assertEquals([
            'comments' => [
                new Comment\Doc('/** Doc comment */', 2, 6, 1),
            ],
            'startLine' => 3,
            'endLine' => 7,
            'startTokenPos' => 3,
            'endTokenPos' => 21,
        ], $fn->getAttributes());

        $param = $fn->params[0];
        $this->assertInstanceOf(Node\Param::class, $param);
        $this->assertEquals([
            'startLine' => 3,
            'endLine' => 3,
            'startTokenPos' => 7,
            'endTokenPos' => 7,
        ], $param->getAttributes());

        /** @var Stmt\Echo_ $echo */
        $echo = $fn->stmts[0];
        $this->assertInstanceOf(Stmt\Echo_::class, $echo);
        $this->assertEquals([
            'comments' => [
                new Comment("// Line\n", 4, 49, 12),
                new Comment("// Comments\n", 5, 61, 14),
            ],
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 16,
            'endTokenPos' => 19,
        ], $echo->getAttributes());

        /** @var \PhpParser\Node\Expr\Variable $var */
        $var = $echo->exprs[0];
        $this->assertInstanceOf(Expr\Variable::class, $var);
        $this->assertEquals([
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 18,
            'endTokenPos' => 18,
        ], $var->getAttributes());
    }

    public function testInvalidToken() {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The lexer returned an invalid token (id=999, value=foobar)');
        $lexer = new InvalidTokenLexer;
        $parser = $this->getParser($lexer);
        $parser->parse('dummy');
    }

    /**
     * @dataProvider provideTestExtraAttributes
     */
    public function testExtraAttributes($code, $expectedAttributes) {
        $parser = $this->getParser(new Lexer\Emulative);
        $stmts = $parser->parse("<?php $code;");
        $node = $stmts[0] instanceof Stmt\Expression ? $stmts[0]->expr : $stmts[0];
        $attributes = $node->getAttributes();
        foreach ($expectedAttributes as $name => $value) {
            $this->assertSame($value, $attributes[$name]);
        }
    }

    public function provideTestExtraAttributes(): \Iterator
    {
        yield ['0', ['kind' => Scalar\LNumber::KIND_DEC]];
        yield ['9', ['kind' => Scalar\LNumber::KIND_DEC]];
        yield ['07', ['kind' => Scalar\LNumber::KIND_OCT]];
        yield ['0xf', ['kind' => Scalar\LNumber::KIND_HEX]];
        yield ['0XF', ['kind' => Scalar\LNumber::KIND_HEX]];
        yield ['0b1', ['kind' => Scalar\LNumber::KIND_BIN]];
        yield ['0B1', ['kind' => Scalar\LNumber::KIND_BIN]];
        yield ['[]', ['kind' => Expr\Array_::KIND_SHORT]];
        yield ['array()', ['kind' => Expr\Array_::KIND_LONG]];
        yield ["'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]];
        yield ["b'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]];
        yield ["B'foo'", ['kind' => String_::KIND_SINGLE_QUOTED]];
        yield ['"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ['b"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ['B"foo"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ['"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ['b"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ['B"foo$bar"', ['kind' => String_::KIND_DOUBLE_QUOTED]];
        yield ["<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<<STR\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<<\"STR\"\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["b<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["B<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<< \t 'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<<'\xff'\n\xff\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => "\xff", 'docIndentation' => '']];
        yield ["<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["b<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["B<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<< \t \"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']];
        yield ["<<<STR\n    STR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '    ']];
        yield ["<<<STR\n\tSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => "\t"]];
        yield ["<<<'STR'\n    Foo\n  STR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '  ']];
        yield ["die", ['kind' => Expr\Exit_::KIND_DIE]];
        yield ["die('done')", ['kind' => Expr\Exit_::KIND_DIE]];
        yield ["exit", ['kind' => Expr\Exit_::KIND_EXIT]];
        yield ["exit(1)", ['kind' => Expr\Exit_::KIND_EXIT]];
        yield ["?>Foo", ['hasLeadingNewline' => false]];
        yield ["?>\nFoo", ['hasLeadingNewline' => true]];
        yield ["namespace Foo;", ['kind' => Stmt\Namespace_::KIND_SEMICOLON]];
        yield ["namespace Foo {}", ['kind' => Stmt\Namespace_::KIND_BRACED]];
        yield ["namespace {}", ['kind' => Stmt\Namespace_::KIND_BRACED]];
        yield ["(float) 5.0", ['kind' => Expr\Cast\Double::KIND_FLOAT]];
        yield ["(double) 5.0", ['kind' => Expr\Cast\Double::KIND_DOUBLE]];
        yield ["(real) 5.0", ['kind' => Expr\Cast\Double::KIND_REAL]];
        yield [" (  REAL )  5.0", ['kind' => Expr\Cast\Double::KIND_REAL]];
    }
}

class InvalidTokenLexer extends Lexer
{
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) : int {
        $value = 'foobar';
        return 999;
    }
}
