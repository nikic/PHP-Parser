<?php

require_once dirname(__FILE__) . '/CodeTestAbstract.php';

class PHPParser_Tests_PrettyPrinterTest extends PHPParser_Tests_CodeTestAbstract
{
    /**
     * @dataProvider provideTestPrettyPrint
     * @covers PHPParser_PrettyPrinter_Zend<extended>
     */
    public function testPrettyPrint($name, $code, $dump) {
        $parser = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
        $prettyPrinter = new PHPParser_PrettyPrinter_Default;

        $stmts = $parser->parse($code);
        $this->assertEquals(
            $this->canonicalize($dump),
            $this->canonicalize($prettyPrinter->prettyPrint($stmts)),
            $name
        );
    }

    public function provideTestPrettyPrint() {
        return $this->getTests(dirname(__FILE__) . '/../../code/prettyPrinter', 'test');
    }
}