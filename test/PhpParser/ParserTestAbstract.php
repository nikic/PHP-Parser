<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;

abstract class ParserTestAbstract extends \PHPUnit\Framework\TestCase {
    /** @returns Parser */
    abstract protected function getParser(Lexer $lexer);

    public function testParserThrowsSyntaxError(): void {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Syntax error, unexpected EOF on line 1');
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php foo');
    }

    public function testParserThrowsSpecialError(): void {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot use foo as self because \'self\' is a special class name on line 1');
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php use foo as self;');
    }

    public function testParserThrowsLexerError(): void {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Unterminated comment on line 1');
        $parser = $this->getParser(new Lexer());
        $parser->parse('<?php /*');
    }

    public function testAttributeAssignment(): void {
        $lexer = new Lexer();

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
                new Comment\Doc('/** Doc comment */',
                    2, 6, 1, 2, 23, 1),
            ],
            'startLine' => 3,
            'endLine' => 7,
            'startTokenPos' => 3,
            'endTokenPos' => 21,
            'startFilePos' => 25,
            'endFilePos' => 86,
        ], $fn->getAttributes());

        $param = $fn->params[0];
        $this->assertInstanceOf(Node\Param::class, $param);
        $this->assertEquals([
            'startLine' => 3,
            'endLine' => 3,
            'startTokenPos' => 7,
            'endTokenPos' => 7,
            'startFilePos' => 39,
            'endFilePos' => 40,
        ], $param->getAttributes());

        /** @var Stmt\Echo_ $echo */
        $echo = $fn->stmts[0];
        $this->assertInstanceOf(Stmt\Echo_::class, $echo);
        $this->assertEquals([
            'comments' => [
                new Comment("// Line",
                    4, 49, 12, 4, 55, 12),
                new Comment("// Comments",
                    5, 61, 14, 5, 71, 14),
            ],
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 16,
            'endTokenPos' => 19,
            'startFilePos' => 77,
            'endFilePos' => 84,
        ], $echo->getAttributes());

        /** @var \PhpParser\Node\Expr\Variable $var */
        $var = $echo->exprs[0];
        $this->assertInstanceOf(Expr\Variable::class, $var);
        $this->assertEquals([
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 18,
            'endTokenPos' => 18,
            'startFilePos' => 82,
            'endFilePos' => 83,
        ], $var->getAttributes());
    }

    public function testInvalidToken(): void {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The lexer returned an invalid token (id=999, value=foobar)');
        $lexer = new InvalidTokenLexer();
        $parser = $this->getParser($lexer);
        $parser->parse('dummy');
    }

    /**
     * @dataProvider provideTestExtraAttributes
     */
    public function testExtraAttributes($code, $expectedAttributes): void {
        $parser = $this->getParser(new Lexer\Emulative());
        $stmts = $parser->parse("<?php $code;");
        $node = $stmts[0] instanceof Stmt\Expression ? $stmts[0]->expr : $stmts[0];
        $attributes = $node->getAttributes();
        foreach ($expectedAttributes as $name => $value) {
            $this->assertSame($value, $attributes[$name]);
        }
    }

    public static function provideTestExtraAttributes() {
        return [
            ['0', ['kind' => Scalar\Int_::KIND_DEC]],
            ['9', ['kind' => Scalar\Int_::KIND_DEC]],
            ['07', ['kind' => Scalar\Int_::KIND_OCT]],
            ['0xf', ['kind' => Scalar\Int_::KIND_HEX]],
            ['0XF', ['kind' => Scalar\Int_::KIND_HEX]],
            ['0b1', ['kind' => Scalar\Int_::KIND_BIN]],
            ['0B1', ['kind' => Scalar\Int_::KIND_BIN]],
            ['0o7', ['kind' => Scalar\Int_::KIND_OCT]],
            ['0O7', ['kind' => Scalar\Int_::KIND_OCT]],
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
            ["<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<<STR\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<<\"STR\"\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["b<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["B<<<'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<< \t 'STR'\nSTR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<<'\xff'\n\xff\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => "\xff", 'docIndentation' => '']],
            ["<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["b<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["B<<<\"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<< \t \"STR\"\n\$a\nSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '']],
            ["<<<STR\n    STR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => '    ']],
            ["<<<STR\n\tSTR\n", ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR', 'docIndentation' => "\t"]],
            ["<<<'STR'\n    Foo\n  STR\n", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR', 'docIndentation' => '  ']],
            ["die", ['kind' => Expr\Exit_::KIND_DIE]],
            ["die('done')", ['kind' => Expr\Exit_::KIND_DIE]],
            ["exit", ['kind' => Expr\Exit_::KIND_EXIT]],
            ["exit(1)", ['kind' => Expr\Exit_::KIND_EXIT]],
            ["?>Foo", ['hasLeadingNewline' => false]],
            ["?>\nFoo", ['hasLeadingNewline' => true]],
            ["namespace Foo;", ['kind' => Stmt\Namespace_::KIND_SEMICOLON]],
            ["namespace Foo {}", ['kind' => Stmt\Namespace_::KIND_BRACED]],
            ["namespace {}", ['kind' => Stmt\Namespace_::KIND_BRACED]],
            ["(float) 5.0", ['kind' => Expr\Cast\Double::KIND_FLOAT]],
            ["(double) 5.0", ['kind' => Expr\Cast\Double::KIND_DOUBLE]],
            ["(real) 5.0", ['kind' => Expr\Cast\Double::KIND_REAL]],
            [" (  REAL )  5.0", ['kind' => Expr\Cast\Double::KIND_REAL]],
        ];
    }

    public function testListKindAttribute(): void {
        $parser = $this->getParser(new Lexer\Emulative());
        $stmts = $parser->parse('<?php list(list($x)) = $y; [[$x]] = $y;');
        $this->assertSame($stmts[0]->expr->var->getAttribute('kind'), Expr\List_::KIND_LIST);
        $this->assertSame($stmts[0]->expr->var->items[0]->value->getAttribute('kind'), Expr\List_::KIND_LIST);
        $this->assertSame($stmts[1]->expr->var->getAttribute('kind'), Expr\List_::KIND_ARRAY);
        $this->assertSame($stmts[1]->expr->var->items[0]->value->getAttribute('kind'), Expr\List_::KIND_ARRAY);
    }

    public function testGetTokens(): void {
        $lexer = new Lexer();
        $parser = $this->getParser($lexer);
        $parser->parse('<?php echo "Foo";');
        $this->assertEquals([
            new Token(\T_OPEN_TAG, '<?php ', 1, 0),
            new Token(\T_ECHO, 'echo', 1, 6),
            new Token(\T_WHITESPACE, ' ', 1, 10),
            new Token(\T_CONSTANT_ENCAPSED_STRING, '"Foo"', 1, 11),
            new Token(ord(';'), ';', 1, 16),
            new Token(0, "\0", 1, 17),
        ], $parser->getTokens());
    }
}

class InvalidTokenLexer extends Lexer {
    public function tokenize(string $code, ?ErrorHandler $errorHandler = null): array {
        return [
            new Token(999, 'foobar', 42),
        ];
    }
}
