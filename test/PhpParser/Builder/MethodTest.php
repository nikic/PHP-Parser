<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Scalar\String;
use PhpParser\Node\Stmt;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function createMethodBuilder($name) {
        return new Method($name);
    }

    public function testModifiers() {
        $node = $this->createMethodBuilder('test')
            ->makePublic()
            ->makeAbstract()
            ->makeStatic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'type' => Stmt\Class_::MODIFIER_PUBLIC
                        | Stmt\Class_::MODIFIER_ABSTRACT
                        | Stmt\Class_::MODIFIER_STATIC,
                'stmts' => null,
            )),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makeProtected()
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'type' => Stmt\Class_::MODIFIER_PROTECTED
                        | Stmt\Class_::MODIFIER_FINAL
            )),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makePrivate()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'type' => Stmt\Class_::MODIFIER_PRIVATE
            )),
            $node
        );
    }

    public function testReturnByRef() {
        $node = $this->createMethodBuilder('test')
            ->makeReturnByRef()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'byRef' => true
            )),
            $node
        );
    }

    public function testParams() {
        $param1 = new Node\Param('test1');
        $param2 = new Node\Param('test2');
        $param3 = new Node\Param('test3');

        $node = $this->createMethodBuilder('test')
            ->addParam($param1)
            ->addParams(array($param2, $param3))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'params' => array($param1, $param2, $param3)
            )),
            $node
        );
    }

    public function testStmts() {
        $stmt1 = new Print_(new String('test1'));
        $stmt2 = new Print_(new String('test2'));
        $stmt3 = new Print_(new String('test3'));

        $node = $this->createMethodBuilder('test')
            ->addStmt($stmt1)
            ->addStmts(array($stmt2, $stmt3))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassMethod('test', array(
                'stmts' => array($stmt1, $stmt2, $stmt3)
            )),
            $node
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot add statements to an abstract method
     */
    public function testAddStmtToAbstractMethodError() {
        $this->createMethodBuilder('test')
            ->makeAbstract()
            ->addStmt(new Print_(new String('test')))
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot make method with statements abstract
     */
    public function testMakeMethodWithStmtsAbstractError() {
        $this->createMethodBuilder('test')
            ->addStmt(new Print_(new String('test')))
            ->makeAbstract()
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected parameter node, got "Name"
     */
    public function testInvalidParamError() {
        $this->createMethodBuilder('test')
            ->addParam(new Node\Name('foo'))
        ;
    }
}