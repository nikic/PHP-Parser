<?php

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Stmt;
use PHPUnit\Framework\TestCase;

class InterfaceTest extends TestCase
{
    /** @var Interface_ */
    protected $builder;

    protected function setUp() {
        $this->builder = new Interface_('Contract');
    }

    private function dump($node) {
        $pp = new \PhpParser\PrettyPrinter\Standard;
        return $pp->prettyPrint([$node]);
    }

    public function testEmpty() {
        $contract = $this->builder->getNode();
        $this->assertInstanceOf('PhpParser\Node\Stmt\Interface_', $contract);
        $this->assertEquals(new Node\Identifier('Contract'), $contract->name);
    }

    public function testExtending() {
        $contract = $this->builder->extend('Space\Root1', 'Root2')->getNode();
        $this->assertEquals(
            new Stmt\Interface_('Contract', [
                'extends' => [
                    new Node\Name('Space\Root1'),
                    new Node\Name('Root2')
                ],
            ]), $contract
        );
    }

    public function testAddMethod() {
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder->addStmt($method)->getNode();
        $this->assertSame([$method], $contract->stmts);
    }

    public function testAddConst() {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458.0))
        ]);
        $contract = $this->builder->addStmt($const)->getNode();
        $this->assertSame(299792458.0, $contract->stmts[0]->consts[0]->value->value);
    }

    public function testOrder() {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458))
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        $this->assertInstanceOf('PhpParser\Node\Stmt\ClassConst', $contract->stmts[0]);
        $this->assertInstanceOf('PhpParser\Node\Stmt\ClassMethod', $contract->stmts[1]);
    }

    public function testDocComment() {
        $node = $this->builder
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(new Stmt\Interface_('Contract', [], [
            'comments' => [new Comment\Doc('/** Test */')]
        ]), $node);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_PropertyProperty"
     */
    public function testInvalidStmtError() {
        $this->builder->addStmt(new Stmt\PropertyProperty('invalid'));
    }

    public function testFullFunctional() {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458))
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        eval($this->dump($contract));

        $this->assertTrue(interface_exists('Contract', false));
    }
}

