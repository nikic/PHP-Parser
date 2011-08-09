<?php

class Unit_NodeTraverserTest extends PHPUnit_Framework_TestCase
{
    function getTestNode() {
        return array(
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
                    )),
                )
            )),
        );
    }

    function testTraverse() {
        $node = $this->getTestNode();

        $visitor   = new Unit_NodeVisitor;
        $traverser = new PHPParser_NodeTraverser;

        $traverser->addVisitor($visitor);
        $traverser->traverse($node);

        $this->assertEquals($node, $visitor->beforeTraverseNode);

        $this->assertEquals(
            array(
                'Stmt_Namespace',
                'Name',
                'Stmt_Echo',
                'Scalar_String',
                'Expr_Print',
                'Scalar_String',
            ),
            $visitor->enteredNodes
        );

        $this->assertEquals(
            array(
                'Name',
                'Scalar_String',
                'Stmt_Echo',
                'Scalar_String',
                'Expr_Print',
                'Stmt_Namespace',
            ),
            $visitor->leftNodes
        );

        $this->assertEquals($node, $visitor->afterTraverseNode);
    }

    function testModifyingTraverse() {
        $node = $this->getTestNode();

        $visitor   = new Unit_ModifyingNodeVisitor;
        $traverser = new PHPParser_NodeTraverser;

        $traverser->addVisitor($visitor);
        $traverser->traverse($node);

        $this->assertEquals(
            array(
                new PHPParser_Node_Stmt_Echo(array(
                    'exprs' => array(
                        new PHPParser_Node_Scalar_String(array(
                            'value' => 'Foo Bar',
                            'isBinary' => false,
                            'type' => PHPParser_Node_Scalar_String::SINGLE_QUOTED
                        ))
                    )
                )),
            ),
            $node
        );
    }
}

class Unit_NodeVisitor extends PHPParser_NodeVisitorAbstract
{
    public $beforeTraverseNode;
    public $enteredNodes;
    public $leftNodes;
    public $afterTraverseNode;

    public function __construct() {
        $this->enteredNodes = $this->leftNodes = array();
    }

    public function beforeTraverse(&$node) {
        $this->beforeTraverseNode = $node;
    }

    public function enterNode(PHPParser_NodeAbstract &$node) {
        $this->enteredNodes[] = $node->getType();
    }

    public function leaveNode(PHPParser_NodeAbstract &$node) {
        $this->leftNodes[] = $node->getType();
    }

    public function afterTraverse(&$node) {
        $this->afterTraverseNode = $node;
    }
}

class Unit_ModifyingNodeVisitor extends PHPParser_NodeVisitorAbstract
{
    public function leaveNode(PHPParser_NodeAbstract &$node) {
        // delete namespace nodes by merging them
        if ($node instanceof PHPParser_Node_Stmt_Namespace) {
            return $node->stmts;
        // remove print nodes completely
        } elseif ($node instanceof PHPParser_Node_Expr_Print) {
            return false;
        // change string contents to 'Foo Bar'
        } elseif ($node instanceof PHPParser_Node_Scalar_String) {
            $node->value = 'Foo Bar';
        }
    }
}