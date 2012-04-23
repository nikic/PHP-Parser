<?php

class PHPParser_Tests_BuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateClassBuilder() {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf('PHPParser_Builder_Class', $factory->class('Test'));
    }

    public function testCreateMethodBuilder() {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf('PHPParser_Builder_Method', $factory->method('test'));
    }

    public function testCreateParamBuilder() {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf('PHPParser_Builder_Param', $factory->param('test'));
    }

    public function testCreatePropertyBuilder() {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf('PHPParser_Builder_Property', $factory->property('test'));
    }

    public function testCreateFunctionBuilder() {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf('PHPParser_Builder_Function', $factory->function('test'));
    }
}