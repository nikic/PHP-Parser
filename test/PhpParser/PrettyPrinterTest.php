<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

class PrettyPrinterTest extends CodeTestAbstract {
    /** @return array{0: Parser, 1: PrettyPrinter} */
    private function createParserAndPrinter(array $options): array {
        $parserVersion = $options['parserVersion'] ?? $options['version'] ?? null;
        $printerVersion = $options['version'] ?? null;
        $indent = isset($options['indent']) ? json_decode($options['indent']) : null;
        $factory = new ParserFactory();
        $parser = $factory->createForVersion($parserVersion !== null
            ? PhpVersion::fromString($parserVersion) : PhpVersion::getNewestSupported());
        $prettyPrinter = new Standard([
            'phpVersion' => $printerVersion !== null ? PhpVersion::fromString($printerVersion) : null,
            'indent' => $indent,
        ]);
        return [$parser, $prettyPrinter];
    }

    protected function doTestPrettyPrintMethod($method, $name, $code, $expected, $modeLine) {
        [$parser, $prettyPrinter] = $this->createParserAndPrinter($this->parseModeLine($modeLine));
        $output = canonicalize($prettyPrinter->$method($parser->parse($code)));
        $this->assertSame($expected, $output, $name);
    }

    /**
     * @dataProvider provideTestPrettyPrint
     */
    public function testPrettyPrint($name, $code, $expected, $mode): void {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $expected, $mode);
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     */
    public function testPrettyPrintFile($name, $code, $expected, $mode): void {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $expected, $mode);
    }

    public static function provideTestPrettyPrint() {
        return self::getTests(__DIR__ . '/../code/prettyPrinter', 'test');
    }

    public static function provideTestPrettyPrintFile() {
        return self::getTests(__DIR__ . '/../code/prettyPrinter', 'file-test');
    }

    public function testPrettyPrintExpr(): void {
        $prettyPrinter = new Standard();
        $expr = new Expr\BinaryOp\Mul(
            new Expr\BinaryOp\Plus(new Expr\Variable('a'), new Expr\Variable('b')),
            new Expr\Variable('c')
        );
        $this->assertEquals('($a + $b) * $c', $prettyPrinter->prettyPrintExpr($expr));

        $expr = new Expr\Closure([
            'stmts' => [new Stmt\Return_(new String_("a\nb"))]
        ]);
        $this->assertEquals("function () {\n    return 'a\nb';\n}", $prettyPrinter->prettyPrintExpr($expr));
    }

    public function testCommentBeforeInlineHTML(): void {
        $prettyPrinter = new PrettyPrinter\Standard();
        $comment = new Comment\Doc("/**\n * This is a comment\n */");
        $stmts = [new Stmt\InlineHTML('Hello World!', ['comments' => [$comment]])];
        $expected = "<?php\n\n/**\n * This is a comment\n */\n?>\nHello World!";
        $this->assertSame($expected, $prettyPrinter->prettyPrintFile($stmts));
    }

    public function testArraySyntaxDefault(): void {
        $prettyPrinter = new Standard(['shortArraySyntax' => true]);
        $expr = new Expr\Array_([
            new Node\ArrayItem(new String_('val'), new String_('key'))
        ]);
        $expected = "['key' => 'val']";
        $this->assertSame($expected, $prettyPrinter->prettyPrintExpr($expr));
    }

    /**
     * @dataProvider provideTestKindAttributes
     */
    public function testKindAttributes($node, $expected): void {
        $prttyPrinter = new PrettyPrinter\Standard();
        $result = $prttyPrinter->prettyPrintExpr($node);
        $this->assertSame($expected, $result);
    }

    public static function provideTestKindAttributes() {
        $nowdoc = ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'STR'];
        $heredoc = ['kind' => String_::KIND_HEREDOC, 'docLabel' => 'STR'];
        return [
            // Defaults to single quoted
            [new String_('foo'), "'foo'"],
            // Explicit single/double quoted
            [new String_('foo', ['kind' => String_::KIND_SINGLE_QUOTED]), "'foo'"],
            [new String_('foo', ['kind' => String_::KIND_DOUBLE_QUOTED]), '"foo"'],
            // Fallback from doc string if no label
            [new String_('foo', ['kind' => String_::KIND_NOWDOC]), "'foo'"],
            [new String_('foo', ['kind' => String_::KIND_HEREDOC]), '"foo"'],
            // Fallback if string contains label
            [new String_("A\nB\nC", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'A']), "'A\nB\nC'"],
            [new String_("A\nB\nC", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'B']), "'A\nB\nC'"],
            [new String_("A\nB\nC", ['kind' => String_::KIND_NOWDOC, 'docLabel' => 'C']), "'A\nB\nC'"],
            [new String_("STR;", $nowdoc), "'STR;'"],
            [new String_("STR,", $nowdoc), "'STR,'"],
            [new String_(" STR", $nowdoc), "' STR'"],
            [new String_("\tSTR", $nowdoc), "'\tSTR'"],
            [new String_("STR\x80", $heredoc), '"STR\x80"'],
            // Doc string if label not contained (or not in ending position)
            [new String_("foo", $nowdoc), "<<<'STR'\nfoo\nSTR"],
            [new String_("foo", $heredoc), "<<<STR\nfoo\nSTR"],
            [new String_("STRx", $nowdoc), "<<<'STR'\nSTRx\nSTR"],
            [new String_("xSTR", $nowdoc), "<<<'STR'\nxSTR\nSTR"],
            [new String_("STRä", $nowdoc), "<<<'STR'\nSTRä\nSTR"],
            [new String_("STR\x80", $nowdoc), "<<<'STR'\nSTR\x80\nSTR"],
            // Empty doc string variations (encapsed variant does not occur naturally)
            [new String_("", $nowdoc), "<<<'STR'\nSTR"],
            [new String_("", $heredoc), "<<<STR\nSTR"],
            [new InterpolatedString([new InterpolatedStringPart('')], $heredoc), "<<<STR\nSTR"],
            // Isolated \r in doc string
            [new String_("\r", $heredoc), "<<<STR\n\\r\nSTR"],
            [new String_("\r", $nowdoc), "'\r'"],
            [new String_("\rx", $nowdoc), "<<<'STR'\n\rx\nSTR"],
            // Encapsed doc string variations
            [new InterpolatedString([new InterpolatedStringPart('foo')], $heredoc), "<<<STR\nfoo\nSTR"],
            [new InterpolatedString([new InterpolatedStringPart('foo'), new Expr\Variable('y')], $heredoc), "<<<STR\nfoo{\$y}\nSTR"],
            [new InterpolatedString([new Expr\Variable('y'), new InterpolatedStringPart("STR\n")], $heredoc), "<<<STR\n{\$y}STR\n\nSTR"],
            // Encapsed doc string fallback
            [new InterpolatedString([new Expr\Variable('y'), new InterpolatedStringPart("\nSTR")], $heredoc), '"{$y}\\nSTR"'],
            [new InterpolatedString([new InterpolatedStringPart("STR\n"), new Expr\Variable('y')], $heredoc), '"STR\\n{$y}"'],
            [new InterpolatedString([new InterpolatedStringPart("STR")], $heredoc), '"STR"'],
            [new InterpolatedString([new InterpolatedStringPart("\nSTR"), new Expr\Variable('y')], $heredoc), '"\nSTR{$y}"'],
            [new InterpolatedString([new InterpolatedStringPart("STR\x80"), new Expr\Variable('y')], $heredoc), '"STR\x80{$y}"'],
        ];
    }

    /** @dataProvider provideTestUnnaturalLiterals */
    public function testUnnaturalLiterals($node, $expected): void {
        $prttyPrinter = new PrettyPrinter\Standard();
        $result = $prttyPrinter->prettyPrintExpr($node);
        $this->assertSame($expected, $result);
    }

    public static function provideTestUnnaturalLiterals() {
        return [
            [new Int_(-1), '-1'],
            [new Int_(-PHP_INT_MAX - 1), '(-' . PHP_INT_MAX . '-1)'],
            [new Int_(-1, ['kind' => Int_::KIND_BIN]), '-0b1'],
            [new Int_(-1, ['kind' => Int_::KIND_OCT]), '-01'],
            [new Int_(-1, ['kind' => Int_::KIND_HEX]), '-0x1'],
            [new Float_(\INF), '1.0E+1000'],
            [new Float_(-\INF), '-1.0E+1000'],
            [new Float_(-\NAN), '\NAN'],
        ];
    }

    public function testPrettyPrintWithError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot pretty-print AST with Error nodes');
        $stmts = [new Stmt\Expression(
            new Expr\PropertyFetch(new Expr\Variable('a'), new Expr\Error())
        )];
        $prettyPrinter = new PrettyPrinter\Standard();
        $prettyPrinter->prettyPrint($stmts);
    }

    public function testPrettyPrintWithErrorInClassConstFetch(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot pretty-print AST with Error nodes');
        $stmts = [new Stmt\Expression(
            new Expr\ClassConstFetch(new Name('Foo'), new Expr\Error())
        )];
        $prettyPrinter = new PrettyPrinter\Standard();
        $prettyPrinter->prettyPrint($stmts);
    }

    /**
     * @dataProvider provideTestFormatPreservingPrint
     */
    public function testFormatPreservingPrint($name, $code, $modification, $expected, $modeLine): void {
        [$parser, $printer] = $this->createParserAndPrinter($this->parseModeLine($modeLine));
        $traverser = new NodeTraverser(new NodeVisitor\CloningVisitor());

        $oldStmts = $parser->parse($code);
        $oldTokens = $parser->getTokens();

        $newStmts = $traverser->traverse($oldStmts);

        /** @var callable $fn */
        eval(<<<CODE
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Modifiers;
\$fn = function(&\$stmts) { $modification };
CODE
        );
        $fn($newStmts);

        $newCode = $printer->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
        $this->assertSame(canonicalize($expected), canonicalize($newCode), $name);
    }

    public static function provideTestFormatPreservingPrint() {
        return self::getTests(__DIR__ . '/../code/formatPreservation', 'test', 3);
    }

    /**
     * @dataProvider provideTestRoundTripPrint
     */
    public function testRoundTripPrint($name, $code, $expected, $modeLine): void {
        /**
         * This test makes sure that the format-preserving pretty printer round-trips for all
         * the pretty printer tests (i.e. returns the input if no changes occurred).
         */

        [$parser, $printer] = $this->createParserAndPrinter($this->parseModeLine($modeLine));
        $traverser = new NodeTraverser(new NodeVisitor\CloningVisitor());

        try {
            $oldStmts = $parser->parse($code);
        } catch (Error $e) {
            // Can't do a format-preserving print on a file with errors
            return;
        }

        $oldTokens = $parser->getTokens();

        $newStmts = $traverser->traverse($oldStmts);

        $newCode = $printer->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
        $this->assertSame(canonicalize($code), canonicalize($newCode), $name);
    }

    public static function provideTestRoundTripPrint() {
        return array_merge(
            self::getTests(__DIR__ . '/../code/prettyPrinter', 'test'),
            self::getTests(__DIR__ . '/../code/parser', 'test')
        );
    }

    public function testWindowsNewline(): void {
        $prettyPrinter = new Standard([
            'newline' => "\r\n",
            'phpVersion' => PhpVersion::fromComponents(7, 2),
        ]);
        $stmts = [
            new Stmt\If_(new Int_(1), [
                'stmts' => [
                    new Stmt\Echo_([new String_('Hello')]),
                    new Stmt\Echo_([new String_('World')]),
                ],
            ]),
        ];
        $code = $prettyPrinter->prettyPrint($stmts);
        $this->assertSame("if (1) {\r\n    echo 'Hello';\r\n    echo 'World';\r\n}", $code);
        $code = $prettyPrinter->prettyPrintFile($stmts);
        $this->assertSame("<?php\r\n\r\nif (1) {\r\n    echo 'Hello';\r\n    echo 'World';\r\n}", $code);

        $stmts = [new Stmt\InlineHTML('Hello world')];
        $code = $prettyPrinter->prettyPrintFile($stmts);
        $this->assertSame("Hello world", $code);

        $stmts = [
            new Stmt\Expression(new String_('Test', [
                'kind' => String_::KIND_NOWDOC,
                'docLabel' => 'STR'
            ])),
            new Stmt\Expression(new String_('Test 2', [
                'kind' => String_::KIND_HEREDOC,
                'docLabel' => 'STR'
            ])),
            new Stmt\Expression(new InterpolatedString([new InterpolatedStringPart('Test 3')], [
                'kind' => String_::KIND_HEREDOC,
                'docLabel' => 'STR'
            ])),
        ];
        $code = $prettyPrinter->prettyPrint($stmts);
        $this->assertSame(
            "<<<'STR'\r\nTest\r\nSTR;\r\n<<<STR\r\nTest 2\r\nSTR;\r\n<<<STR\r\nTest 3\r\nSTR\r\n;",
            $code);
    }

    public function testInvalidNewline(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Option "newline" must be one of "\n" or "\r\n"');
        new PrettyPrinter\Standard(['newline' => 'foo']);
    }

    public function testInvalidIndent(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Option "indent" must either be all spaces or a single tab');
        new PrettyPrinter\Standard(['indent' => "\t  "]);
    }
}
