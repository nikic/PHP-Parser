<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Variable;

class ParamTest extends \PHPUnit\Framework\TestCase {
    public function testNoModifiers(): void {
        $node = new Param(new Variable('foo'));

        $this->assertFalse($node->isPromoted());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isReadonly());
        $this->assertFalse($node->isPublicSet());
        $this->assertFalse($node->isProtectedSet());
        $this->assertFalse($node->isPrivateSet());
    }

    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers(string $modifier): void {
        $node = new Param(new Variable('foo'));
        $node->flags = constant(Modifiers::class . '::' . strtoupper($modifier));
        $this->assertTrue($node->isPromoted());
        $this->assertTrue($node->{'is' . $modifier}());
    }

    public static function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['readonly'],
        ];
    }

    public function testSetVisibility() {
        $node = new Param(new Variable('foo'));
        $node->flags = Modifiers::PRIVATE_SET;
        $this->assertTrue($node->isPrivateSet());
        $this->assertTrue($node->isPublic());
        $node->flags = Modifiers::PROTECTED_SET;
        $this->assertTrue($node->isProtectedSet());
        $this->assertTrue($node->isPublic());
        $node->flags = Modifiers::PUBLIC_SET;
        $this->assertTrue($node->isPublicSet());
        $this->assertTrue($node->isPublic());
    }

    public function testPromotedPropertyWithoutVisibilityModifier(): void {
        $node = new Param(new Variable('foo'));
        $get = new PropertyHook('get', null);
        $node->hooks[] = $get;

        $this->assertTrue($node->isPromoted());
        $this->assertTrue($node->isPublic());
    }

    public function testNonPromotedPropertyIsNotPublic(): void {
        $node = new Param(new Variable('foo'));
        $this->assertFalse($node->isPublic());
    }
}
