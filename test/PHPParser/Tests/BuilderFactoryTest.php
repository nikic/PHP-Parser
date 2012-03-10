<?php

class PHPParser_Tests_BuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateClassBuilder() {
        $factory = new PHPParser_BuilderFactory;

        $builder = $factory->class('Test');
        $this->assertInstanceOf('PHPParser_Builder_Class', $builder);

        $this->assertEquals(new PHPParser_Node_Stmt_Class('Test'), $builder->getNode());
    }
}