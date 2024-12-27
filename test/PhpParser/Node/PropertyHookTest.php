<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;

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

    public function testGetStmts(): void {
        $expr = new Variable('test');
        $get = new PropertyHook('get', $expr);
        $this->assertEquals([new Return_($expr)], $get->getStmts());

        // TODO: This is incorrect.
        $set = new PropertyHook('set', $expr);
        $this->assertEquals([new Expression($expr)], $set->getStmts());
    }

    public function testSetStmtsUnknownHook(): void {
        $expr = new Variable('test');
        $get = new PropertyHook('foobar', $expr);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unknown property hook "foobar"');
        $get->getStmts();
    }
}
