<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

class CompatibilityTest extends \PHPUnit\Framework\TestCase {
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAliases1() {
        $var = new Expr\Variable('x');
        $node = new Node\ClosureUse($var);
        $this->assertTrue($node instanceof Expr\ClosureUse);
        $node = new Node\ArrayItem($var);
        $this->assertTrue($node instanceof Expr\ArrayItem);
        $node = new Node\StaticVar($var);
        $this->assertTrue($node instanceof Stmt\StaticVar);
        $node = new Scalar\Float_(1.0);
        $this->assertTrue($node instanceof Scalar\DNumber);
        $node = new Scalar\Int_(1);
        $this->assertTrue($node instanceof Scalar\LNumber);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAliases2() {
        $var = new Expr\Variable('x');
        $node = new Node\Expr\ClosureUse($var);
        $this->assertTrue($node instanceof Node\ClosureUse);
        $node = new Node\Expr\ArrayItem($var);
        $this->assertTrue($node instanceof Node\ArrayItem);
        $node = new Node\Stmt\StaticVar($var);
        $this->assertTrue($node instanceof Node\StaticVar);
        $node = new Node\Scalar\DNumber(1.0);
        $this->assertTrue($node instanceof Scalar\Float_);
        $node = new Node\Scalar\LNumber(1);
        $this->assertTrue($node instanceof Scalar\Int_);
    }
}
