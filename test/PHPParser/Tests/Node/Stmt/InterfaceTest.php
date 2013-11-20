<?php

require_once __DIR__ . '/OoPattern.php';

class PHPParser_Tests_Node_Stmt_InterfaceTest extends PHPParser_Tests_Node_Stmt_OoPattern
{

    protected function createDefinitionType(array $methods)
    {
        return new PHPParser_Node_Stmt_Interface('Foo', array(
            'stmts' => array(
                new PHPParser_Node_Stmt_Const(array()),
                $methods[0],
                new PHPParser_Node_Stmt_Const(array()),
                $methods[1],
                new PHPParser_Node_Stmt_Const(array()),
                $methods[2],
            )
        ));
    }

}