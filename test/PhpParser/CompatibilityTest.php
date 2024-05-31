<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

class CompatibilityTest extends \PHPUnit\Framework\TestCase {
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAliases1(): void {
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
        $part = new InterpolatedStringPart('foo');
        $this->assertTrue($part instanceof Scalar\EncapsedStringPart);
        $node = new Scalar\InterpolatedString([$part]);
        $this->assertTrue($node instanceof Scalar\Encapsed);
        $node = new Node\DeclareItem('strict_types', new Scalar\Int_(1));
        $this->assertTrue($node instanceof Stmt\DeclareDeclare);
        $node = new Node\PropertyItem('x');
        $this->assertTrue($node instanceof Stmt\PropertyProperty);
        $node = new Node\UseItem(new Name('X'));
        $this->assertTrue($node instanceof Stmt\UseUse);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAliases2(): void {
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
        $part = new Node\Scalar\EncapsedStringPart('foo');
        $this->assertTrue($part instanceof Node\InterpolatedStringPart);
        $node = new Scalar\Encapsed([$part]);
        $this->assertTrue($node instanceof Scalar\InterpolatedString);
        $node = new Stmt\DeclareDeclare('strict_types', new Scalar\LNumber(1));
        $this->assertTrue($node instanceof Node\DeclareItem);
        $node = new Stmt\PropertyProperty('x');
        $this->assertTrue($node instanceof Node\PropertyItem);
        $node = new Stmt\UseUse(new Name('X'));
        $this->assertTrue($node instanceof Node\UseItem);
    }
}
