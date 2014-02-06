<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

class ClassTest extends \PHPUnit_Framework_TestCase
{
    protected function createClassBuilder($class) {
        return new Class_($class);
    }

    public function testExtendsImplements() {
        $node = $this->createClassBuilder('SomeLogger')
            ->extend('BaseLogger')
            ->implement('Namespaced\Logger', new Name('SomeInterface'))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('SomeLogger', array(
                'extends' => new Name('BaseLogger'),
                'implements' => array(
                    new Name('Namespaced\Logger'),
                    new Name('SomeInterface')
                ),
            )),
            $node
        );
    }

    public function testAbstract() {
        $node = $this->createClassBuilder('Test')
            ->makeAbstract()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', array(
                'type' => Stmt\Class_::MODIFIER_ABSTRACT
            )),
            $node
        );
    }

    public function testFinal() {
        $node = $this->createClassBuilder('Test')
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', array(
                'type' => Stmt\Class_::MODIFIER_FINAL
            )),
            $node
        );
    }

    public function testStatementOrder() {
        $method = new Stmt\ClassMethod('testMethod');
        $property = new Stmt\Property(
            Stmt\Class_::MODIFIER_PUBLIC,
            array(new Stmt\PropertyProperty('testProperty'))
        );
        $const = new Stmt\ClassConst(array(
            new Node\Const_('TEST_CONST', new Node\Scalar\String('ABC'))
        ));
        $use = new Stmt\TraitUse(array(new Name('SomeTrait')));

        $node = $this->createClassBuilder('Test')
            ->addStmt($method)
            ->addStmt($property)
            ->addStmts(array($const, $use))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', array(
                'stmts' => array($use, $const, $property, $method)
            )),
            $node
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_Echo"
     */
    public function testInvalidStmtError() {
        $this->createClassBuilder('Test')
            ->addStmt(new Stmt\Echo_(array()))
        ;
    }
}