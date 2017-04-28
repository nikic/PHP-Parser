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

        list($parser5, $parser7) = $this->createParsers($modes);
        $output5 = $this->getParseOutput($parser5, $code, $modes);
        $output7 = $this->getParseOutput($parser7, $code, $modes);

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

    public function createParsers(array $modes) {
        $lexer = new Lexer\Emulative(array('usedAttributes' => array(
            'startLine', 'endLine', 'startFilePos', 'endFilePos', 'comments'
        )));

        return [
            new Parser\Php5($lexer),
            new Parser\Php7($lexer),
        ];
    }

    public function getParseOutput(Parser $parser, $code, array $modes) {
        $dumpPositions = isset($modes['positions']);

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
