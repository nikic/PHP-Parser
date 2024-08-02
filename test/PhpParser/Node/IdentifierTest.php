<?php declare(strict_types=1);

namespace PhpParser\Node;

class IdentifierTest extends \PHPUnit\Framework\TestCase {
    public function testConstructorThrows(): void {
        self::expectException(\InvalidArgumentException::class);
        new Identifier('');
    }

    public function testToString(): void {
        $identifier = new Identifier('Foo');

        $this->assertSame('Foo', (string) $identifier);
        $this->assertSame('Foo', $identifier->toString());
        $this->assertSame('foo', $identifier->toLowerString());
    }

    /** @dataProvider provideTestIsSpecialClassName */
    public function testIsSpecialClassName($identifier, $expected): void {
        $identifier = new Identifier($identifier);
        $this->assertSame($expected, $identifier->isSpecialClassName());
    }

    public static function provideTestIsSpecialClassName() {
        return [
            ['self', true],
            ['PARENT', true],
            ['Static', true],
            ['other', false],
        ];
    }
}
