<?php

namespace PhpParser;

class NodeAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $attributes = array(
            'startLine' => 10,
            'comments'  => array(
                new Comment('// Comment' . "\n"),
                new Comment\Doc('/** doc comment */'),
            ),
        );

        /** @var $node NodeAbstract */
        $node = $this->getMockForAbstractClass(
            'PhpParser\NodeAbstract',
            array(
                array(
                    'subNode' => 'value'
                ),
                $attributes
            ),
            'PhpParser_Node_Dummy'
        );

        $this->assertEquals('Dummy', $node->getType());
        $this->assertEquals(array('subNode'), $node->getSubNodeNames());
        $this->assertEquals(10, $node->getLine());
        $this->assertEquals('/** doc comment */', $node->getDocComment());
        $this->assertEquals('value', $node->subNode);
        $this->assertTrue(isset($node->subNode));
        $this->assertEquals($attributes, $node->getAttributes());

        return $node;
    }

    /**
     * @depends testConstruct
     */
    public function testGetDocComment(Node $node) {
        $this->assertEquals('/** doc comment */', $node->getDocComment());
        array_pop($node->getAttribute('comments')); // remove doc comment
        $this->assertNull($node->getDocComment());
        array_pop($node->getAttribute('comments')); // remove comment
        $this->assertNull($node->getDocComment());
    }

    /**
     * @depends testConstruct
     */
    public function testChange(Node $node) {
        // change of line
        $node->setLine(15);
        $this->assertEquals(15, $node->getLine());

        // direct modification
        $node->subNode = 'newValue';
        $this->assertEquals('newValue', $node->subNode);

        // indirect modification
        $subNode =& $node->subNode;
        $subNode = 'newNewValue';
        $this->assertEquals('newNewValue', $node->subNode);

        // removal
        unset($node->subNode);
        $this->assertFalse(isset($node->subNode));
    }

    public function testAttributes() {
        /** @var $node Node */
        $node = $this->getMockForAbstractClass('PhpParser\NodeAbstract');

        $this->assertEmpty($node->getAttributes());

        $node->setAttribute('key', 'value');
        $this->assertTrue($node->hasAttribute('key'));
        $this->assertEquals('value', $node->getAttribute('key'));

        $this->assertFalse($node->hasAttribute('doesNotExist'));
        $this->assertNull($node->getAttribute('doesNotExist'));
        $this->assertEquals('default', $node->getAttribute('doesNotExist', 'default'));

        $node->setAttribute('null', null);
        $this->assertTrue($node->hasAttribute('null'));
        $this->assertNull($node->getAttribute('null'));
        $this->assertNull($node->getAttribute('null', 'default'));

        $this->assertEquals(
            array(
                'key'  => 'value',
                'null' => null,
            ),
            $node->getAttributes()
        );
    }
}