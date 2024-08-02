<?php declare(strict_types=1);

namespace PhpParser;

class NodeDumperTest extends \PHPUnit\Framework\TestCase {
    private function canonicalize($string) {
        return str_replace("\r\n", "\n", $string);
    }

    /**
     * @dataProvider provideTestDump
     */
    public function testDump($node, $dump): void {
        $dumper = new NodeDumper();

        $this->assertSame($this->canonicalize($dump), $this->canonicalize($dumper->dump($node)));
    }

    public static function provideTestDump() {
        return [
            [
                [],
'array(
)'
            ],
            [
                ['Foo', 'Bar', 'Key' => 'FooBar'],
'array(
    0: Foo
    1: Bar
    Key: FooBar
)'
            ],
            [
                new Node\Name(['Hallo', 'World']),
'Name(
    name: Hallo\World
)'
            ],
            [
                new Node\Expr\Array_([
                    new Node\ArrayItem(new Node\Scalar\String_('Foo'))
                ]),
'Expr_Array(
    items: array(
        0: ArrayItem(
            key: null
            value: Scalar_String(
                value: Foo
            )
            byRef: false
            unpack: false
        )
    )
)'
            ],
        ];
    }

    public function testDumpWithPositions(): void {
        $parser = (new ParserFactory())->createForHostVersion();
        $dumper = new NodeDumper(['dumpPositions' => true]);

        $code = "<?php\n\$a = 1;\necho \$a;";
        $expected = <<<'OUT'
array(
    0: Stmt_Expression[2:1 - 2:7](
        expr: Expr_Assign[2:1 - 2:6](
            var: Expr_Variable[2:1 - 2:2](
                name: a
            )
            expr: Scalar_Int[2:6 - 2:6](
                value: 1
            )
        )
    )
    1: Stmt_Echo[3:1 - 3:8](
        exprs: array(
            0: Expr_Variable[3:6 - 3:7](
                name: a
            )
        )
    )
)
OUT;

        $stmts = $parser->parse($code);
        $dump = $dumper->dump($stmts, $code);

        $this->assertSame($this->canonicalize($expected), $this->canonicalize($dump));
    }

    public function testError(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can only dump nodes and arrays.');
        $dumper = new NodeDumper();
        $dumper->dump(new \stdClass());
    }
}
