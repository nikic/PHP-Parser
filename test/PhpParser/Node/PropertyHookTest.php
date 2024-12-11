<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;

class PropertyHookTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier): void {
        $node = new PropertyHook(
            'get',
            null,
            [
                'flags' => constant(Modifiers::class . '::' . strtoupper($modifier)),
            ]
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers(): void {
        $node = new PropertyHook('get', null);

        $this->assertFalse($node->isFinal());
    }

    public static function provideModifiers() {
        return [
            ['final'],
        ];
    }
}
