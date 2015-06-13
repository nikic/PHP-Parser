<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

require_once __DIR__ . '/CodeTestAbstract.php';

class PrettyPrinterTest extends CodeTestAbstract
{
    protected function doTestPrettyPrintMethod($method, $name, $code, $expected) {
        $parser = new Parser(new Lexer\Emulative);
        $prettyPrinter = new Standard;

        $stmts = $parser->parse($code);
        $this->assertSame(
            $expected,
            $this->canonicalize($prettyPrinter->$method($stmts)),
            $name
        );
    }

    /**
     * @dataProvider provideTestPrettyPrint
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrint($name, $code, $expected) {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $expected);
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrintFile($name, $code, $expected) {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $expected);
    }

    public function provideTestPrettyPrint() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'test');
    }

    public function provideTestPrettyPrintFile() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'file-test');
    }

    public function testPrettyPrintExpr() {
        $prettyPrinter = new Standard;
        $expr = new Expr\BinaryOp\Mul(
            new Expr\BinaryOp\Plus(new Expr\Variable('a'), new Expr\Variable('b')),
            new Expr\Variable('c')
        );
        $this->assertEquals('($a + $b) * $c', $prettyPrinter->prettyPrintExpr($expr));

        $expr = new Expr\Closure(array(
            'stmts' => array(new Stmt\Return_(new String_("a\nb")))
        ));
        $this->assertEquals("function () {\n    return 'a\nb';\n}", $prettyPrinter->prettyPrintExpr($expr));
    }
}
