<?php

namespace PhpParser;

class BuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className) {
        $factory = new BuilderFactory;
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public function provideTestFactory() {
        return array(
            array('namespace', 'PhpParser\Builder\Namespace_'),
            array('use',       'PhpParser\Builder\Use_'),
            array('class',     'PhpParser\Builder\Class_'),
            array('interface', 'PhpParser\Builder\Interface_'),
            array('trait',     'PhpParser\Builder\Trait_'),
            array('method',    'PhpParser\Builder\Method'),
            array('function',  'PhpParser\Builder\Function_'),
            array('property',  'PhpParser\Builder\Property'),
            array('param',     'PhpParser\Builder\Param'),
        );
    }
}
