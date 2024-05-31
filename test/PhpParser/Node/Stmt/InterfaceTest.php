<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\Scalar\String_;

class InterfaceTest extends \PHPUnit\Framework\TestCase {
    public function testGetMethods(): void {
        $methods = [
            new ClassMethod('foo'),
            new ClassMethod('bar'),
        ];
        $interface = new Interface_('Foo', [
            'stmts' => [
                new Node\Stmt\ClassConst([new Node\Const_('C1', new Node\Scalar\String_('C1'))]),
                $methods[0],
                new Node\Stmt\ClassConst([new Node\Const_('C2', new Node\Scalar\String_('C2'))]),
                $methods[1],
                new Node\Stmt\ClassConst([new Node\Const_('C3', new Node\Scalar\String_('C3'))]),
            ]
        ]);

        $this->assertSame($methods, $interface->getMethods());
    }

    public function testGetConstants(): void {
        $constants = [
            new ClassConst([new \PhpParser\Node\Const_('foo', new String_('foo_value'))]),
            new ClassConst([new \PhpParser\Node\Const_('bar', new String_('bar_value'))]),
        ];
        $class = new Interface_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $constants[0],
                new ClassMethod('fooBar'),
                $constants[1],
            ]
        ]);

        $this->assertSame($constants, $class->getConstants());
    }
}
