<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Scalar\String_;

class ClassTest extends \PHPUnit\Framework\TestCase
{
    public function testIsAbstract() {
        $class = new Class_('Foo', ['type' => Class_::MODIFIER_ABSTRACT]);
        $this->assertTrue($class->isAbstract());

        $class = new Class_('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal() {
        $class = new Class_('Foo', ['type' => Class_::MODIFIER_FINAL]);
        $this->assertTrue($class->isFinal());

        $class = new Class_('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function testGetTraitUses() {
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

    public function testGetMethods() {
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

    public function testGetConstants() {
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

    public function testGetProperties()
    {
        $properties = [
            new Property(Class_::MODIFIER_PUBLIC, [new PropertyProperty('foo')]),
            new Property(Class_::MODIFIER_PUBLIC, [new PropertyProperty('bar')]),
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

    public function testGetMethod() {
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
