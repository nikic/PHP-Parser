<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;

class ClassConstTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier): void {
        $node = new ClassConst(
            [], // invalid
            constant(Modifiers::class . '::' . strtoupper($modifier))
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers(): void {
        $node = new ClassConst([], 0);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isFinal());
    }

    public static function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['final'],
        ];
    }
}
