<?php

namespace PhpParser\Node\Scalar;

class MagicConstTest extends \PHPUnit_Framework_TestCase
{
    public function testClassConstruct() {
        $node = new MagicConst\Class_();
        $this->assertEquals(array('__CLASS__'), $node->name->parts);
    }

    public function testDirConstruct() {
        $node = new MagicConst\Dir();
        $this->assertEquals(array('__DIR__'), $node->name->parts);
    }

    public function testFileConstruct() {
        $node = new MagicConst\File();
        $this->assertEquals(array('__FILE__'), $node->name->parts);
    }

    public function testLineConstruct() {
        $node = new MagicConst\Line();
        $this->assertEquals(array('__LINE__'), $node->name->parts);
    }

    public function testMethodConstruct() {
        $node = new MagicConst\Method();
        $this->assertEquals(array('__METHOD__'), $node->name->parts);
    }

    public function testNamespaceConstruct() {
        $node = new MagicConst\Namespace_();
        $this->assertEquals(array('__NAMESPACE__'), $node->name->parts);
    }

    public function testTraitConstruct() {
        $node = new MagicConst\Trait_();
        $this->assertEquals(array('__TRAIT__'), $node->name->parts);
    }
}
