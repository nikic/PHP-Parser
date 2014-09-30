<?php

namespace PhpParser;

require_once __DIR__ . '/CodeTestAbstract.php';

class PrettyPrinterTest extends CodeTestAbstract
{
    protected function doTestPrettyPrintMethod($method, $name, $code, $dump) {
        $parser = new Parser(new Lexer\Emulative);
        $prettyPrinter = new PrettyPrinter\Standard;

        $stmts = $parser->parse($code);
        $this->assertSame(
            $this->canonicalize($dump),
            $this->canonicalize($prettyPrinter->$method($stmts)),
            $name
        );
    }

    /**
     * @dataProvider provideTestPrettyPrint
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrint($name, $code, $dump) {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $dump);
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrintFile($name, $code, $dump) {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $dump);
    }

    public function provideTestPrettyPrint() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'test');
    }

    public function provideTestPrettyPrintFile() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'file-test');
    }
}