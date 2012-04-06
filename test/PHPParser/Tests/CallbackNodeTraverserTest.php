<?php

class PHPParser_Tests_CallbackNodeTraverserTest extends PHPUnit_Framework_TestCase
{
    public function testTraverse()
    {
        $while = new PHPParser_Node_Stmt_While(
            $expr = new PHPParser_Node_Expr_ConstFetch(
                 $name = new PHPParser_Node_Name(array('true'))));

        $callback = $this->getMock('PHPParser_NodeTraversalCallback');
        $callback->expects($this->at(0))
            ->method('shouldTraverse')
            ->with($while)
            ->will($this->returnValue(true));
        $callback->expects($this->at(1))
            ->method('shouldTraverse')
            ->with($expr)
            ->will($this->returnValue(true));
        $callback->expects($this->at(2))
            ->method('shouldTraverse')
            ->with($name)
            ->will($this->returnValue(true));

        $callback->expects($this->at(3))
            ->method('visit')
            ->with($name);
        $callback->expects($this->at(4))
            ->method('visit')
            ->with($expr);
        $callback->expects($this->at(5))
            ->method('visit')
            ->with($while);

        PHPParser_CallbackNodeTraverser::traverseWithCallback(array($while), $callback);
    }

    public function testTraverseSkipsChildren()
    {
        $while = new PHPParser_Node_Stmt_While(
            $expr = new PHPParser_Node_Expr_ConstFetch(
                 $name = new PHPParser_Node_Name(array('true'))));

        $callback = $this->getMock('PHPParser_NodeTraversalCallback');
        $callback->expects($this->at(0))
            ->method('shouldTraverse')
            ->with($while)
            ->will($this->returnValue(true));
        $callback->expects($this->at(1))
            ->method('shouldTraverse')
            ->with($expr)
            ->will($this->returnValue(false));

        $callback->expects($this->at(2))
            ->method('visit')
            ->with($expr);
        $callback->expects($this->at(3))
            ->method('visit')
            ->with($while);

        PHPParser_CallbackNodeTraverser::traverseWithCallback(array($while), $callback);

    }
}