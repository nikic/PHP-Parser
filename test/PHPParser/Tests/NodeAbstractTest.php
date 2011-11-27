<?php

class PHPParser_Tests_NodeAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $node = $this->getMockForAbstractClass(
            'PHPParser_NodeAbstract',
            array(
                array(
                    'subNode' => 'value'
                ),
                10,
                '/** doc comment */'
            ),
            'PHPParser_Node_Dummy'
        );

        $this->assertEquals(10, $node->getLine());
        $this->assertEquals('/** doc comment */', $node->getDocComment());
        $this->assertEquals('Dummy', $node->getType());
        $this->assertEquals('value', $node->subNode);
        $this->assertTrue(isset($node->subNode));

        return $node;
    }

    /**
     * @depends testConstruct
     */
    public function testChange(PHPParser_Node $node) {
        $node->setLine(15);
        $this->assertEquals(15, $node->getLine());

        $node->setDocComment('/** other doc comment */');
        $this->assertEquals('/** other doc comment */', $node->getDocComment());

        $node->subNode = 'newValue';
        $this->assertEquals('newValue', $node->subNode);

        unset($node->subNode);
        $this->assertFalse(isset($node->subNode));
    }
}