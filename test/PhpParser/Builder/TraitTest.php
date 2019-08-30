<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\TraitUse;

class TraitTest extends \PHPUnit\Framework\TestCase
{
    protected function createTraitBuilder($class) {
        return new Trait_($class);
    }

    public function testStmtAddition() {
        $method1 = new Stmt\ClassMethod('test1');
        $method2 = new Stmt\ClassMethod('test2');
        $method3 = new Stmt\ClassMethod('test3');
        $prop = new Stmt\Property(Stmt\Class_::MODIFIER_PUBLIC, [
            new Stmt\PropertyProperty('test')
        ]);
        $use = new Stmt\TraitUse([new Name('OtherTrait')]);
        $trait = $this->createTraitBuilder('TestTrait')
            ->setDocComment('/** Nice trait */')
            ->addStmt($method1)
            ->addStmts([$method2, $method3])
            ->addStmt($prop)
            ->addStmt($use)
            ->getNode();
        $this->assertEquals(new Stmt\Trait_('TestTrait', [
            'stmts' => [$use, $prop, $method1, $method2, $method3]
        ], [
            'comments' => [
                new Comment\Doc('/** Nice trait */')
            ]
        ]), $trait);
    }

    public function testInvalidStmtError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unexpected node of type "Stmt_Echo"');
        $this->createTraitBuilder('Test')
            ->addStmt(new Stmt\Echo_([]))
        ;
    }

    public function testGetMethods() {
        $methods = [
            new ClassMethod('foo'),
            new ClassMethod('bar'),
            new ClassMethod('fooBar'),
        ];
        $trait = new Stmt\Trait_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $methods[0],
                new ClassConst([]),
                $methods[1],
                new Property(0, []),
                $methods[2],
            ]
        ]);

        $this->assertSame($methods, $trait->getMethods());
    }

    public function testGetProperties()
    {
        $properties = [
            new Property(Class_::MODIFIER_PUBLIC, [new PropertyProperty('foo')]),
            new Property(Class_::MODIFIER_PUBLIC, [new PropertyProperty('bar')]),
        ];
        $trait = new Stmt\Trait_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $properties[0],
                new ClassConst([]),
                $properties[1],
                new ClassMethod('fooBar'),
            ]
        ]);

        $this->assertSame($properties, $trait->getProperties());
    }
}
