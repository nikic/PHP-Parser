<?php

namespace PhpParser;

/* The autoloader is already active at this point, so we only check effects here. */

class AutoloaderTest extends \PHPUnit_Framework_TestCase {
    public function testLegacyNames() {
        $lexer = new \PHPParser_Lexer;
        $parser = new \PHPParser_Parser($lexer);
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default;

        $this->assertInstanceof('PhpParser\Lexer', $lexer);
        $this->assertInstanceof('PhpParser\Parser', $parser);
        $this->assertInstanceof('PhpParser\PrettyPrinter\Standard', $prettyPrinter);
    }

    public function testClassExists() {
        $this->assertTrue(class_exists('PhpParser\NodeVisitorAbstract'));
        $this->assertTrue(class_exists('PHPParser_NodeVisitor_NameResolver'));

        $this->assertFalse(class_exists('PhpParser\FooBar'));
        $this->assertFalse(class_exists('PHPParser_FooBar'));
    }
}