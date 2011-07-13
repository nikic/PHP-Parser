<?php

class Unit_NodeTraverserTest extends PHPUnit_Framework_TestCase
{
    function testTraverse() {
        $node = array(
            new PHPParser_Node_Stmt_Namespace(array(
                'name' => new PHPParser_Node_Name(array(
                    'parts' => array('Foo', 'Bar')
                )),
                'stmts' => array(
                    new PHPParser_Node_Stmt_Echo(array(
                        'exprs' => array(
                            new PHPParser_Node_Scalar_String(array(
                                'value' => 'Hallo World',
                                'isBinary' => false,
                                'type' => PHPParser_Node_Scalar_String::SINGLE_QUOTED
                            ))
                        )
                    )),
                    new PHPParser_Node_Expr_Print(array(
                        'expr' => new PHPParser_Node_Scalar_String(array(
                            'value' => 'Hallo World, again!',
                            'isBinary' => false,
                            'type' => PHPParser_Node_Scalar_String::SINGLE_QUOTED
                        ))
                    ))
                )
            )),
        );

        // on enter
        $this->visitedNodes = array();
        $nodeTraverser = new PHPParser_NodeTraverser;
        $nodeTraverser->addVisitor(array($this, 'visitNode'), PHPParser_NodeTraverser::ON_ENTER);
        $nodeTraverser->traverse($node);
        $this->assertEquals(
            array(
                'Stmt_Namespace',
                'Name',
                'Stmt_Echo',
                'Scalar_String',
                'Expr_Print',
                'Scalar_String',
            ),
            $this->visitedNodes
        );

        // on leave
        $this->visitedNodes = array();
        $nodeTraverser = new PHPParser_NodeTraverser;
        $nodeTraverser->addVisitor(array($this, 'visitNode'), PHPParser_NodeTraverser::ON_LEAVE);
        $nodeTraverser->traverse($node);
        $this->assertEquals(
            array(
                'Name',
                'Scalar_String',
                'Stmt_Echo',
                'Scalar_String',
                'Expr_Print',
                'Stmt_Namespace',
            ),
            $this->visitedNodes
        );
    }

    private $visitedNodes;

    public function visitNode(PHPParser_NodeAbstract $node) {
        $this->visitedNodes[] = $node->getType();
    }
}