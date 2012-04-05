<?php

class PHPParser_Tests_NodeVisitor_NodeConnectorTest extends PHPUnit_Framework_TestCase
{
    private $traverser;

    public function testEnterNode()
    {
        $switch = new PHPParser_Node_Stmt_Switch($cond = new PHPParser_Node_Expr_ConstFetch(new PHPParser_Node_Name(array('true'))), array(
            $case = new PHPParser_Node_Stmt_Case(null, array(
                $assign = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable('foo'), new PHPParser_Node_Scalar_String('foo')),
                $break = new PHPParser_Node_Stmt_Break(),
            ))
        ));
        $this->traverser->traverse(array($switch));

        $this->assertNull($switch->getAttribute('parent'));
        $this->assertNull($switch->getAttribute('next'));
        $this->assertNull($switch->getAttribute('previous'));

        $this->assertSame($switch, $case->getAttribute('parent'));
        $this->assertNull($case->getAttribute('next'));
        $this->assertSame($cond, $case->getAttribute('previous'));

        $this->assertSame($case, $assign->getAttribute('parent'));
        $this->assertSame($break, $assign->getAttribute('next'));
        $this->assertNull($assign->getAttribute('previous'));

        $this->assertSame($case, $break->getAttribute('parent'));
        $this->assertNull($break->getAttribute('next'));
        $this->assertSame($assign, $break->getAttribute('previous'));
    }

    protected function setUp()
    {
        $this->traverser = new PHPParser_NodeTraverser();
        $this->traverser->addVisitor(new PHPParser_NodeVisitor_NodeConnector());
    }
}