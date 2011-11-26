<?php

class PHPParser_Tests_codeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestCode
     */
    public function testCode($name, $code, $xml) {
        $parser     = new PHPParser_Parser;
        $serializer = new PHPParser_Serializer_XML;

        $stmts = $parser->parse(new PHPParser_Lexer($code));
        $this->assertEquals($xml, trim($serializer->serialize($stmts)), $name);
    }

    public function provideTestCode() {
        $tests = array();

        $it = new RecursiveDirectoryIterator(dirname(__FILE__) . '/../../code');
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new RegexIterator($it, '~\.test$~');

        foreach ($it as $file) {
            $fileContents = file_get_contents($file);

            // normalize EOL to Unix
            $fileContents = str_replace(array("\r\n", "\r"), "\n", $fileContents);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace('/@@\{(.*?)\}@@/e', '$1', $fileContents);

            $tests[] = array_map('trim', explode('-----', $fileContents));
        }

        return $tests;
    }
}