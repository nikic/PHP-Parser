<?php

class PHPParser_Tests_Node_Stmt_ClassTest extends PHPUnit_Framework_TestCase
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
}