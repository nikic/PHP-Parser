<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;

class CompatibilityTest extends \PHPUnit\Framework\TestCase
{
    public function testAliases1() {
        $node = new Node\ClosureUse(new Expr\Variable('x'));
        $this->assertInstanceOf(Expr\ClosureUse::class, $node);
    }

    public function testAliases2() {
        $node = new Node\Expr\ClosureUse(new Expr\Variable('x'));
        $this->assertInstanceOf(Node\ClosureUse::class, $node);
    }
}
