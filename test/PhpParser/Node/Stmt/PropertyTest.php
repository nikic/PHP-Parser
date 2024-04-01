<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;

class PropertyTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier) {
        $node = new Property(
            constant(Modifiers::class . '::' . strtoupper($modifier)),
            [] // invalid
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers() {
        $node = new Property(0, []);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isStatic());
        $this->assertFalse($node->isReadonly());
    }

    public function testStaticImplicitlyPublic() {
        $node = new Property(Modifiers::STATIC, []);
        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertTrue($node->isStatic());
        $this->assertFalse($node->isReadonly());
    }

    public function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['static'],
            ['readonly'],
        ];
    }
}
