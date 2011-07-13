<?php

class Unit_NodeDumperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestDump
     */
    public function testDump($node, $dump) {
        $nodeDumper = new PHPParser_NodeDumper;

        $this->assertEquals($dump, $nodeDumper->dump($node));
    }

    public function provideTestDump() {
        return array(
            array(
                array(),
'array(
)'
            ),
            array(
                array('Foo', 'Bar', 'Key' => 'FooBar'),
'array(
    0: Foo
    1: Bar
    Key: FooBar
)'
            ),
            array(
                new PHPParser_Node_Name(array('parts' => array('Hallo', 'World'))),
'Name(
    parts: array(
        0: Hallo
        1: World
    )
)'
            ),
            array(
                new PHPParser_Node_Expr_Array(array('items' => array(
                    new PHPParser_Node_Expr_ArrayItem(array('key' => 'Foo', 'value' => 'Bar', 'byRef' => false))
                ))),
'Expr_Array(
    items: array(
        0: Expr_ArrayItem(
            key: Foo
            value: Bar
            byRef: false
        )
    )
)'
            ),
        );
    }
}