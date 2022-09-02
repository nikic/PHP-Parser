<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;

class CompatibilityTest extends \PHPUnit\Framework\TestCase {
    public function testAliases1() {
        $var = new Expr\Variable('x');
        $node = new Node\ClosureUse($var);
        $this->assertTrue($node instanceof Expr\ClosureUse);
        $node = new Node\ArrayItem($var);
        $this->assertTrue($node instanceof Expr\ArrayItem);
    }

    public function testAliases2() {
        $var = new Expr\Variable('x');
        $node = new Node\Expr\ClosureUse($var);
        $this->assertTrue($node instanceof Node\ClosureUse);
        $node = new Node\Expr\ArrayItem($var);
        $this->assertTrue($node instanceof Node\ArrayItem);
    }
}
