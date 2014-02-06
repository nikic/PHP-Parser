<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Scalar\String;

class FunctionTest extends \PHPUnit_Framework_TestCase
{
    public function createFunctionBuilder($name) {
        return new Function_($name);
    }

    public function testReturnByRef() {
        $node = $this->createFunctionBuilder('test')
            ->makeReturnByRef()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Function_('test', array(
                'byRef' => true
            )),
            $node
        );
    }

    public function testParams() {
        $param1 = new Node\Param('test1');
        $param2 = new Node\Param('test2');
        $param3 = new Node\Param('test3');

        $node = $this->createFunctionBuilder('test')
            ->addParam($param1)
            ->addParams(array($param2, $param3))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Function_('test', array(
                'params' => array($param1, $param2, $param3)
            )),
            $node
        );
    }

    public function testStmts() {
        $stmt1 = new Print_(new String('test1'));
        $stmt2 = new Print_(new String('test2'));
        $stmt3 = new Print_(new String('test3'));

        $node = $this->createFunctionBuilder('test')
            ->addStmt($stmt1)
            ->addStmts(array($stmt2, $stmt3))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Function_('test', array(
                'stmts' => array($stmt1, $stmt2, $stmt3)
            )),
            $node
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected parameter node, got "Name"
     */
    public function testInvalidParamError() {
        $this->createFunctionBuilder('test')
            ->addParam(new Node\Name('foo'))
        ;
    }
}