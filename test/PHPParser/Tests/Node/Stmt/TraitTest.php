<?php

require_once dirname(__FILE__) . '/OoPattern.php';

class PHPParser_Tests_Node_Stmt_TraitTest extends PHPParser_Tests_Node_Stmt_OoPattern
{

    protected function createDefinitionType(array $methods)
    {
        return new PHPParser_Node_Stmt_Trait('Foo', array(
            new PHPParser_Node_Stmt_TraitUse(array()),
            $methods[0],
            new PHPParser_Node_Stmt_Const(array()),
            $methods[1],
            new PHPParser_Node_Stmt_Property(0, array()),
            $methods[2],
        ));
    }

}