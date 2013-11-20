<?php

require_once __DIR__ . '/OoPattern.php';

class PHPParser_Tests_Node_Stmt_ClassTest extends PHPParser_Tests_Node_Stmt_OoPattern
{
    public function testIsAbstract() {
        $class = new PHPParser_Node_Stmt_Class('Foo', array('type' => PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT));
        $this->assertTrue($class->isAbstract());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal() {
        $class = new PHPParser_Node_Stmt_Class('Foo', array('type' => PHPParser_Node_Stmt_Class::MODIFIER_FINAL));
        $this->assertTrue($class->isFinal());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isFinal());
    }

    protected function createDefinitionType(array $methods)
    {
        return new PHPParser_Node_Stmt_Class('Foo', array(
            'stmts' => array(
                new PHPParser_Node_Stmt_TraitUse(array()),
                $methods[0],
                new PHPParser_Node_Stmt_Const(array()),
                $methods[1],
                new PHPParser_Node_Stmt_Property(0, array()),
                $methods[2],
            )
        ));
    }

}