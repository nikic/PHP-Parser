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
}
