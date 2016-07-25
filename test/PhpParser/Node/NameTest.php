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

    public function testSlice() {
        $name = new Name('foo\bar\baz');
        $this->assertEquals(new Name('foo\bar\baz'), $name->slice(0));
        $this->assertEquals(new Name('bar\baz'), $name->slice(1));
        $this->assertEquals(new Name([]), $name->slice(3));
        $this->assertEquals(new Name('foo\bar\baz'), $name->slice(-3));
        $this->assertEquals(new Name('bar\baz'), $name->slice(-2));
        $this->assertEquals(new Name('foo\bar'), $name->slice(0, -1));
        $this->assertEquals(new Name([]), $name->slice(0, -3));
        $this->assertEquals(new Name('bar'), $name->slice(1, -1));
        $this->assertEquals(new Name([]), $name->slice(1, -2));
        $this->assertEquals(new Name('bar'), $name->slice(-2, 1));
        $this->assertEquals(new Name('bar'), $name->slice(-2, -1));
        $this->assertEquals(new Name([]), $name->slice(-2, -2));
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Offset 4 is out of bounds
     */
    public function testSliceOffsetTooLarge() {
        (new Name('foo\bar\baz'))->slice(4);
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Offset -4 is out of bounds
     */
    public function testSliceOffsetTooSmall() {
        (new Name('foo\bar\baz'))->slice(-4);
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Length 4 is out of bounds
     */
    public function testSliceLengthTooLarge() {
        (new Name('foo\bar\baz'))->slice(0, 4);
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Length -4 is out of bounds
     */
    public function testSliceLengthTooSmall() {
        (new Name('foo\bar\baz'))->slice(0, -4);
    }

    public function testConcat() {
        $this->assertEquals(new Name('foo\bar\baz'), Name::concat('foo', 'bar\baz'));
        $this->assertEquals(
            new Name\FullyQualified('foo\bar'),
            Name\FullyQualified::concat(['foo'], new Name('bar'))
        );

        $attributes = ['foo' => 'bar'];
        $this->assertEquals(
            new Name\Relative('foo\bar\baz', $attributes),
            Name\Relative::concat(new Name\FullyQualified('foo\bar'), 'baz', $attributes)
        );

        $this->assertEquals(new Name('foo'), Name::concat([], 'foo'));
        $this->assertEquals(new Name([]), Name::concat([], []));
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
        $name->append(new \stdClass);
    }
}