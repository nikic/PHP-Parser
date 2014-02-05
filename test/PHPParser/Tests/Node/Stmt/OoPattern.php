<?php

/**
 * Template Method for tests on class/interface/trait nodes
 */
abstract class PHPParser_Tests_Node_Stmt_OoPattern extends PHPUnit_Framework_TestCase
{

    abstract protected function createDefinitionType(array $methodList);

    public function testGetMethods()
    {
        $methods = array(
            new PHPParser_Node_Stmt_ClassMethod('foo'),
            new PHPParser_Node_Stmt_ClassMethod('bar'),
            new PHPParser_Node_Stmt_ClassMethod('fooBar'),
        );

        $definition = $this->createDefinitionType($methods);

        $this->assertEquals($methods, $definition->getMethods());
    }

}