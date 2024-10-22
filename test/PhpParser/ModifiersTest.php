<?php declare(strict_types=1);

namespace PhpParser;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ModifiersTest extends TestCase {
    public function testToString() {
        $this->assertSame('public', Modifiers::toString(Modifiers::PUBLIC));
    }

    public function testToStringInvalid() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown modifier 3');
        Modifiers::toString(Modifiers::PUBLIC | Modifiers::PROTECTED);
    }
}
