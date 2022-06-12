<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;

class CompatibilityTest extends \PHPUnit\Framework\TestCase
{
    public function testAliases1() {
        $node = new Node\ClosureUse(new Expr\Variable('x'));
        $this->assertTrue($node instanceof Expr\ClosureUse);
    }

    public function testAliases2() {
        $node = new Node\Expr\ClosureUse(new Expr\Variable('x'));
        $this->assertTrue($node instanceof Node\ClosureUse);
    }
}
