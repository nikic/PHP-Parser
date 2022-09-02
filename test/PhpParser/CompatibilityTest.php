<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class CompatibilityTest extends \PHPUnit\Framework\TestCase {
    /** @runInSeparateProcess */
    public function testAliases1() {
        $var = new Expr\Variable('x');
        $node = new Node\ClosureUse($var);
        $this->assertTrue($node instanceof Expr\ClosureUse);
        $node = new Node\ArrayItem($var);
        $this->assertTrue($node instanceof Expr\ArrayItem);
        $node = new Node\StaticVar($var);
        $this->assertTrue($node instanceof Stmt\StaticVar);
    }

    /** @runInSeparateProcess */
    public function testAliases2() {
        $var = new Expr\Variable('x');
        $node = new Node\Expr\ClosureUse($var);
        $this->assertTrue($node instanceof Node\ClosureUse);
        $node = new Node\Expr\ArrayItem($var);
        $this->assertTrue($node instanceof Node\ArrayItem);
        $node = new Node\Stmt\StaticVar($var);
        $this->assertTrue($node instanceof Node\StaticVar);
    }
}
