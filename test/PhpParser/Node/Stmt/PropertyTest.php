<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;

class PropertyTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier): void {
        $node = new Property(
            constant(Modifiers::class . '::' . strtoupper($modifier)),
            [] // invalid
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers(): void {
        $node = new Property(0, []);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isStatic());
        $this->assertFalse($node->isReadonly());
        $this->assertFalse($node->isPublicSet());
        $this->assertFalse($node->isProtectedSet());
        $this->assertFalse($node->isPrivateSet());
    }

    public function testStaticImplicitlyPublic(): void {
        $node = new Property(Modifiers::STATIC, []);
        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertTrue($node->isStatic());
        $this->assertFalse($node->isReadonly());
    }

    public static function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['static'],
            ['readonly'],
        ];
    }

    public function testSetVisibility() {
        $node = new Property(Modifiers::PRIVATE_SET, []);
        $this->assertTrue($node->isPrivateSet());
        $node = new Property(Modifiers::PROTECTED_SET, []);
        $this->assertTrue($node->isProtectedSet());
        $node = new Property(Modifiers::PUBLIC_SET, []);
        $this->assertTrue($node->isPublicSet());
    }

    public function testIsFinal() {
        $node = new Property(Modifiers::FINAL, []);
        $this->assertTrue($node->isFinal());
    }

    public function testIsAbstract() {
        $node = new Property(Modifiers::ABSTRACT, []);
        $this->assertTrue($node->isAbstract());
    }
}
