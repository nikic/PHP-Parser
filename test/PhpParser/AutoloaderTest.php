<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

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

    public function testPhp7ReservedNames() {
        if (version_compare(PHP_VERSION, '7.0-dev', '>=')) {
            $this->markTestSkipped('Cannot create aliases to reserved names on PHP 7');
        }

        $this->assertTrue(new Expr\Cast\Bool_(new Expr\Variable('foo')) instanceof Expr\Cast\Bool);
        $this->assertTrue(new Expr\Cast\Int_(new Expr\Variable('foo')) instanceof Expr\Cast\Int);

        $this->assertInstanceof('PhpParser\Node\Expr\Cast\Object_', new Expr\Cast\Object(new Expr\Variable('foo')));
        $this->assertInstanceof('PhpParser\Node\Expr\Cast\String_', new Expr\Cast\String(new Expr\Variable('foo')));
        $this->assertInstanceof('PhpParser\Node\Scalar\String_', new Scalar\String('foobar'));
    }

    public function testClassExists() {
        $this->assertTrue(class_exists('PhpParser\NodeVisitorAbstract'));
        $this->assertTrue(class_exists('PHPParser_NodeVisitor_NameResolver'));

        $this->assertFalse(class_exists('PhpParser\FooBar'));
        $this->assertFalse(class_exists('PHPParser_FooBar'));
    }
}
