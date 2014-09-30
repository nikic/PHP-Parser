<?php

namespace PhpParser;

require_once __DIR__ . '/CodeTestAbstract.php';

class ParserTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $dump) {
        $parser = new Parser(new Lexer\Emulative);
        $dumper = new NodeDumper;

        $stmts = $parser->parse($code);
        $this->assertSame(
            $this->canonicalize($dump),
            $this->canonicalize($dumper->dump($stmts)),
            $name
        );
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    /**
     * @dataProvider provideTestParseFail
     */
    public function testParseFail($name, $code, $msg) {
        $parser = new Parser(new Lexer\Emulative);

        try {
            $parser->parse($code);

            $this->fail(sprintf('"%s": Expected Error', $name));
        } catch (Error $e) {
            $this->assertSame($msg, $e->getMessage(), $name);
        }
    }

    public function provideTestParseFail() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test-fail');
    }
}