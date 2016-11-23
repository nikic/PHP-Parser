<?php

namespace PhpParser;

require_once __DIR__ . '/CodeTestAbstract.php';

class CodeParsingTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $expected, $modeLine) {
        if (null !== $modeLine) {
            $modes = array_fill_keys(explode(',', $modeLine), true);
        } else {
            $modes = [];
        }


        $lexer = new Lexer\Emulative(array('usedAttributes' => array(
            'startLine', 'endLine', 'startFilePos', 'endFilePos', 'comments'
        )));
        $parser5 = new Parser\Php5($lexer);
        $parser7 = new Parser\Php7($lexer);

        $dumpPositions = isset($modes['positions']);
        $output5 = $this->getParseOutput($parser5, $code, $dumpPositions);
        $output7 = $this->getParseOutput($parser7, $code, $dumpPositions);

        if (isset($modes['php5'])) {
            $this->assertSame($expected, $output5, $name);
            $this->assertNotSame($expected, $output7, $name);
        } else if (isset($modes['php7'])) {
            $this->assertNotSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        } else {
            $this->assertSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        }
    }

    private function getParseOutput(Parser $parser, $code, $dumpPositions) {
        $errors = new ErrorHandler\Collecting;
        $stmts = $parser->parse($code, $errors);

        $output = '';
        foreach ($errors->getErrors() as $error) {
            $output .= $this->formatErrorMessage($error, $code) . "\n";
        }

        if (null !== $stmts) {
            $dumper = new NodeDumper(['dumpComments' => true, 'dumpPositions' => $dumpPositions]);
            $output .= $dumper->dump($stmts, $code);
        }

        return canonicalize($output);
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    private function formatErrorMessage(Error $e, $code) {
        if ($e->hasColumnInfo()) {
            return $e->getMessageWithColumnInfo($code);
        } else {
            return $e->getMessage();
        }
    }
}
