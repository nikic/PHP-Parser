<?php

class PHPParser_Tests_codeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestCode
     */
    public function testCode($name, $code, $dump) {
        $parser = new PHPParser_Parser;
        $dumper = new PHPParser_NodeDumper;

        $stmts = $parser->parse(new PHPParser_Lexer($code));
        $this->assertEquals(
            $this->canonicalize($dump),
            $this->canonicalize($dumper->dump($stmts)),
            $name
        );
    }

    public function provideTestCode() {
        return $this->getTests('test');
    }

    /**
     * @dataProvider provideTestCodeFail
     */
    public function testCodeFail($name, $code, $msg) {
        $parser = new PHPParser_Parser;

        try {
            $parser->parse(new PHPParser_Lexer($code));

            $this->fail(sprintf('"%s": Expected PHPParser_Error', $name));
        } catch (PHPParser_Error $e) {
            $this->assertEquals($msg, $e->getMessage(), $name);
        }
    }

    public function provideTestCodeFail() {
        return $this->getTests('test-fail');
    }

    protected function getTests($ext) {
        $it = new RecursiveDirectoryIterator(dirname(__FILE__) . '/../../code');
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::LEAVES_ONLY);

        $ext = preg_quote($ext, '~');
        if (version_compare(PHP_VERSION, '5.4.0RC1', '>=')) {
            $it = new RegexIterator($it, '~\.' . $ext . '(-5\.4)?$~');
        } else {
            $it = new RegexIterator($it, '~\.' . $ext . '$~');
        }

        $tests = array();
        foreach ($it as $file) {
            // read file
            $fileContents = file_get_contents($file);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace('/@@\{(.*?)\}@@/e', '$1', $fileContents);

            // parse sections
            $parts = array_map('trim', explode('-----', $fileContents));

            // first part is the name
            $name = array_shift($parts);

            // multiple sections possible with always two forming a pair
            foreach (array_chunk($parts, 2) as $chunk) {
                $tests[] = array($name, $chunk[0], $chunk[1]);
            }
        }

        return $tests;
    }

    protected function canonicalize($str) {
        // trim from both sides
        $str = trim($str);

        // normalize EOL to \n
        $str = str_replace(array("\r\n", "\r"), "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }
}