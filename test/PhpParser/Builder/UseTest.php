<?php

use PhpParser\Builder;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPUnit\Framework\TestCase;

class UseTest extends TestCase
{
    protected function createUseBuilder($name, $type = Stmt\Use_::TYPE_NORMAL) {
        return new Builder\Use_($name, $type);
    }

    public function testCreation() {
        $node = $this->createUseBuilder('Foo\Bar')->getNode();
        $this->assertEquals(new Stmt\Use_(array(
            new Stmt\UseUse(new Name('Foo\Bar'), 'Bar')
        )), $node);

        $node = $this->createUseBuilder(new Name('Foo\Bar'))->as('XYZ')->getNode();
        $this->assertEquals(new Stmt\Use_(array(
            new Stmt\UseUse(new Name('Foo\Bar'), 'XYZ')
        )), $node);

        $node = $this->createUseBuilder('foo\bar', Stmt\Use_::TYPE_FUNCTION)->as('foo')->getNode();
        $this->assertEquals(new Stmt\Use_(array(
            new Stmt\UseUse(new Name('foo\bar'), 'foo')
        ), Stmt\Use_::TYPE_FUNCTION), $node);
    }

    public function testNonExistingMethod() {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Method "foo" does not exist');
        $builder = $this->createUseBuilder('Test');
        $builder->foo();
    }
}
