<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Scalar\String_;

class ClassTest extends \PHPUnit\Framework\TestCase {
    public function testIsAbstract(): void {
        $class = new Class_('Foo', ['type' => Modifiers::ABSTRACT]);
        $this->assertTrue($class->isAbstract());

        $class = new Class_('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal(): void {
        $class = new Class_('Foo', ['type' => Modifiers::FINAL]);
        $this->assertTrue($class->isFinal());

        $class = new Class_('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function testGetTraitUses(): void {
        $traitUses = [
            new TraitUse([new Trait_('foo')]),
            new TraitUse([new Trait_('bar')]),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                $traitUses[0],
                new ClassMethod('fooBar'),
                $traitUses[1],
            ]
        ]);

        $this->assertSame($traitUses, $class->getTraitUses());
    }

    public function testGetMethods(): void {
        $methods = [
            new ClassMethod('foo'),
            new ClassMethod('bar'),
            new ClassMethod('fooBar'),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $methods[0],
                new ClassConst([]),
                $methods[1],
                new Property(0, []),
                $methods[2],
            ]
        ]);

        $this->assertSame($methods, $class->getMethods());
    }

    public function testGetConstants(): void {
        $constants = [
            new ClassConst([new \PhpParser\Node\Const_('foo', new String_('foo_value'))]),
            new ClassConst([new \PhpParser\Node\Const_('bar', new String_('bar_value'))]),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $constants[0],
                new ClassMethod('fooBar'),
                $constants[1],
            ]
        ]);

        $this->assertSame($constants, $class->getConstants());
    }

    public function testGetProperties(): void {
        $properties = [
            new Property(Modifiers::PUBLIC, [new PropertyItem('foo')]),
            new Property(Modifiers::PUBLIC, [new PropertyItem('bar')]),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $properties[0],
                new ClassConst([]),
                $properties[1],
                new ClassMethod('fooBar'),
            ]
        ]);

        $this->assertSame($properties, $class->getProperties());
    }

    public function testGetProperty(): void {
        $properties = [
            $fooProp = new Property(Modifiers::PUBLIC, [new PropertyItem('foo1')]),
            $barProp = new Property(Modifiers::PUBLIC, [new PropertyItem('BAR1')]),
            $fooBarProp = new Property(Modifiers::PUBLIC, [new PropertyItem('foo2'), new PropertyItem('bar2')]),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $properties[0],
                new ClassConst([]),
                $properties[1],
                new ClassMethod('fooBar'),
                $properties[2],
            ]
        ]);

        $this->assertSame($fooProp, $class->getProperty('foo1'));
        $this->assertSame($barProp, $class->getProperty('BAR1'));
        $this->assertSame($fooBarProp, $class->getProperty('foo2'));
        $this->assertSame($fooBarProp, $class->getProperty('bar2'));
        $this->assertNull($class->getProperty('bar1'));
        $this->assertNull($class->getProperty('nonExisting'));
    }

    public function testGetMethod(): void {
        $methodConstruct = new ClassMethod('__CONSTRUCT');
        $methodTest = new ClassMethod('test');
        $class = new Class_('Foo', [
            'stmts' => [
                new ClassConst([]),
                $methodConstruct,
                new Property(0, []),
                $methodTest,
            ]
        ]);

        $this->assertSame($methodConstruct, $class->getMethod('__construct'));
        $this->assertSame($methodTest, $class->getMethod('test'));
        $this->assertNull($class->getMethod('nonExisting'));
    }
}
