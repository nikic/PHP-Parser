<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPUnit\Framework\TestCase;

class TraitTest extends TestCase
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

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_Echo"
     */
    public function testInvalidStmtError() {
        $this->createTraitBuilder('Test')
            ->addStmt(new Stmt\Echo_([]))
        ;
    }
}
