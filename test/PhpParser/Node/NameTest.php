<?php declare(strict_types=1);

namespace PhpParser\Node;

use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testConstruct() {
        $name = new Name(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $name->parts);

        $name = new Name('foo\bar');
        $this->assertSame(['foo', 'bar'], $name->parts);

        $name = new Name($name);
        $this->assertSame(['foo', 'bar'], $name->parts);
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
        $name = new Name('Foo\Bar');

        $this->assertSame('Foo\Bar', (string) $name);
        $this->assertSame('Foo\Bar', $name->toString());
        $this->assertSame('foo\bar', $name->toLowerString());
    }

    public function testSlice() {
        $name = new Name('foo\bar\baz');
        $this->assertEquals(new Name('foo\bar\baz'), $name->slice(0));
        $this->assertEquals(new Name('bar\baz'), $name->slice(1));
        $this->assertNull($name->slice(3));
        $this->assertEquals(new Name('foo\bar\baz'), $name->slice(-3));
        $this->assertEquals(new Name('bar\baz'), $name->slice(-2));
        $this->assertEquals(new Name('foo\bar'), $name->slice(0, -1));
        $this->assertNull($name->slice(0, -3));
        $this->assertEquals(new Name('bar'), $name->slice(1, -1));
        $this->assertNull($name->slice(1, -2));
        $this->assertEquals(new Name('bar'), $name->slice(-2, 1));
        $this->assertEquals(new Name('bar'), $name->slice(-2, -1));
        $this->assertNull($name->slice(-2, -2));
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

        $this->assertEquals(new Name('foo'), Name::concat(null, 'foo'));
        $this->assertEquals(new Name('foo'), Name::concat('foo', null));
        $this->assertNull(Name::concat(null, null));
    }

    public function testNameTypes() {
        $name = new Name('foo');
        $this->assertTrue($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $this->assertSame('foo', $name->toCodeString());

        $name = new Name('foo\bar');
        $this->assertFalse($name->isUnqualified());
        $this->assertTrue($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $this->assertSame('foo\bar', $name->toCodeString());

        $name = new Name\FullyQualified('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertTrue($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $this->assertSame('\foo', $name->toCodeString());

        $name = new Name\Relative('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertTrue($name->isRelative());
        $this->assertSame('namespace\foo', $name->toCodeString());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected string, array of parts or Name instance
     */
    public function testInvalidArg() {
        Name::concat('foo', new \stdClass);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name cannot be empty
     */
    public function testInvalidEmptyString() {
        new Name('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name cannot be empty
     */
    public function testInvalidEmptyArray() {
        new Name([]);
    }

    /** @dataProvider provideTestIsSpecialClassName */
    public function testIsSpecialClassName($name, $expected) {
        $name = new Name($name);
        $this->assertSame($expected, $name->isSpecialClassName());
    }

    public function provideTestIsSpecialClassName() {
        return [
            ['self', true],
            ['PARENT', true],
            ['Static', true],
            ['self\not', false],
            ['not\self', false],
        ];
    }
}
