<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PHPUnit\Framework\TestCase;

class ClassMethodTest extends TestCase
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
     * @param string $modifier Node type modifier
     */
    public function testImplicitPublic(string $modifier)
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
    public function testMagic(string $name) {
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

    public function testFunctionLike() {
        $param = new Param(new Variable('a'));
        $type = new Name('Foo');
        $return = new Return_(new Variable('a'));
        $method = new ClassMethod('test', [
            'byRef' => false,
            'params' => [$param],
            'returnType' => $type,
            'stmts' => [$return],
        ]);

        $this->assertFalse($method->returnsByRef());
        $this->assertSame([$param], $method->getParams());
        $this->assertSame($type, $method->getReturnType());
        $this->assertSame([$return], $method->getStmts());

        $method = new ClassMethod('test', [
            'byRef' => true,
            'stmts' => null,
        ]);

        $this->assertTrue($method->returnsByRef());
        $this->assertNull($method->getStmts());
    }
}
