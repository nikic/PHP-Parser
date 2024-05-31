<?php declare(strict_types=1);

namespace PhpParser;

class PhpVersionTest extends \PHPUnit\Framework\TestCase {
    public function testConstruction(): void {
        $version = PhpVersion::fromComponents(8, 2);
        $this->assertSame(80200, $version->id);

        $version = PhpVersion::fromString('8.2');
        $this->assertSame(80200, $version->id);

        $version = PhpVersion::fromString('8.2.14');
        $this->assertSame(80200, $version->id);

        $version = PhpVersion::fromString('8.2.14rc1');
        $this->assertSame(80200, $version->id);
    }

    public function testInvalidVersion(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid PHP version "8"');
        PhpVersion::fromString('8');
    }

    public function testEquals(): void {
        $php74 = PhpVersion::fromComponents(7, 4);
        $php81 = PhpVersion::fromComponents(8, 1);
        $php82 = PhpVersion::fromComponents(8, 2);
        $this->assertTrue($php81->equals($php81));
        $this->assertFalse($php81->equals($php82));

        $this->assertTrue($php81->older($php82));
        $this->assertFalse($php81->older($php81));
        $this->assertFalse($php81->older($php74));

        $this->assertFalse($php81->newerOrEqual($php82));
        $this->assertTrue($php81->newerOrEqual($php81));
        $this->assertTrue($php81->newerOrEqual($php74));
    }
}
