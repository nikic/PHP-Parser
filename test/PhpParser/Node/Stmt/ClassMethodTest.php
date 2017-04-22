<?php

namespace PhpParser\Node\Stmt;

class ClassMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier) {
        $node = new ClassMethod('foo', array(
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_' . strtoupper($modifier))
        ));

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers() {
        $node = new ClassMethod('foo', array('type' => 0));

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isAbstract());
        $this->assertFalse($node->isFinal());
        $this->assertFalse($node->isStatic());
        $this->assertFalse($node->isMagic());
    }

    public function provideModifiers() {
        return array(
            array('public'),
            array('protected'),
            array('private'),
            array('abstract'),
            array('final'),
            array('static'),
        );
    }

    /**
     * Checks that implicit public modifier detection for method is working
     *
     * @dataProvider implicitPublicModifiers
     *
     * @param integer $modifier Node type modifier
     */
    public function testImplicitPublic($modifier)
    {
        $node = new ClassMethod('foo', array(
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_' . strtoupper($modifier))
        ));

        $this->assertTrue($node->isPublic(), 'Node should be implicitly public');
    }

    public function implicitPublicModifiers() {
        return array(
            array('abstract'),
            array('final'),
            array('static'),
        );
    }

    /**
     * @dataProvider provideMagics
     *
     * @param string $name Node name
     */
    public function testMagic($name) {
        $node = new ClassMethod($name);
        $this->assertTrue($node->isMagic(), 'Method should be magic');
    }

    public function provideMagics() {
        return array(
             array('__construct'),
             array('__DESTRUCT'),
             array('__caLL'),
             array('__callstatic'),
             array('__get'),
             array('__set'),
             array('__isset'),
             array('__unset'),
             array('__sleep'),
             array('__wakeup'),
             array('__tostring'),
             array('__set_state'),
             array('__clone'),
             array('__invoke'),
             array('__debuginfo'),
        );
    }
}
