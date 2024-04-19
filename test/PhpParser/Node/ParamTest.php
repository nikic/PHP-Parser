<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Variable;

class ParamTest extends \PHPUnit\Framework\TestCase {
    public function testNoModifiers() {
        $node = new Param(new Variable('foo'));

        $this->assertFalse($node->isPromoted());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isReadonly());
    }

    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers(string $modifier) {
        $node = new Param(new Variable('foo'));
        $node->flags = constant(Modifiers::class . '::' . strtoupper($modifier));
        $this->assertTrue($node->isPromoted());
        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['readonly'],
        ];
    }

    public function testGetTypeNode() {
        $node = new Param(new Variable('foo'), null, new Name('FooInterface'));
        $this->assertEquals('FooInterface', $node->getTypeNode()->toString());
    }

    public function testToString() {
        $node = new Param(new Variable('foo'));
        $this->assertEquals('foo', $node->toString());
    }
}
