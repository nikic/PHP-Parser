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
}