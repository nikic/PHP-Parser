<?php

namespace PhpParser\Node;

class NameTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $name = new Name(array('foo', 'bar'));
        $this->assertSame(array('foo', 'bar'), $name->parts);

        $name = new Name('foo\bar');
        $this->assertSame(array('foo', 'bar'), $name->parts);
    }

    public function testGet() {
        $name = new Name('foo');
        $this->assertSame('foo', $name->getFirst());
        $this->assertSame('foo', $name->getLast());

        $name = new Name('foo\bar');
        $this->assertSame('foo', $name->getFirst());
        $this->assertSame('bar', $name->getLast());
    }

    public function testToString() {
        $name = new Name('foo\bar');

        $this->assertSame('foo\bar', (string) $name);
        $this->assertSame('foo\bar', $name->toString());
        $this->assertSame('foo_bar', $name->toString('_'));
    }

    public function testSet() {
        $name = new Name('foo');

        $name->set('foo\bar');
        $this->assertSame('foo\bar', $name->toString());

        $name->set(array('foo', 'bar'));
        $this->assertSame('foo\bar', $name->toString());

        $name->set(new Name('foo\bar'));
        $this->assertSame('foo\bar', $name->toString());
    }

    public function testSetFirst() {
        $name = new Name('foo');

        $name->setFirst('bar');
        $this->assertSame('bar', $name->toString());

        $name->setFirst('A\B');
        $this->assertSame('A\B', $name->toString());

        $name->setFirst('C');
        $this->assertSame('C\B', $name->toString());

        $name->setFirst('D\E');
        $this->assertSame('D\E\B', $name->toString());
    }

    public function testSetLast() {
        $name = new Name('foo');

        $name->setLast('bar');
        $this->assertSame('bar', $name->toString());

        $name->setLast('A\B');
        $this->assertSame('A\B', $name->toString());

        $name->setLast('C');
        $this->assertSame('A\C', $name->toString());

        $name->setLast('D\E');
        $this->assertSame('A\D\E', $name->toString());
    }

    public function testAppend() {
        $name = new Name('foo');

        $name->append('bar');
        $this->assertSame('foo\bar', $name->toString());

        $name->append('bar\foo');
        $this->assertSame('foo\bar\bar\foo', $name->toString());
    }

    public function testPrepend() {
        $name = new Name('foo');

        $name->prepend('bar');
        $this->assertSame('bar\foo', $name->toString());

        $name->prepend('foo\bar');
        $this->assertSame('foo\bar\bar\foo', $name->toString());
    }

    public function testIs() {
        $name = new Name('foo');
        $this->assertTrue ($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new Name('foo\bar');
        $this->assertFalse($name->isUnqualified());
        $this->assertTrue ($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new Name\FullyQualified('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertTrue ($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new Name\Relative('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertTrue ($name->isRelative());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage When changing a name you need to pass either a string, an array or a Name node
     */
    public function testInvalidArg() {
        $name = new Name('foo');
        $name->set(new \stdClass);
    }
}