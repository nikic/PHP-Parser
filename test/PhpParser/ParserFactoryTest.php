<?php declare(strict_types=1);

namespace PhpParser;

/* This test is very weak, because PHPUnit's assertEquals assertion is way too slow dealing with the
 * large objects involved here. So we just do some basic instanceof tests instead. */

use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;

class ParserFactoryTest extends \PHPUnit\Framework\TestCase {
    public function testCreate(): void {
        $factory = new ParserFactory();
        $this->assertInstanceOf(Php8::class, $factory->createForNewestSupportedVersion());
        $this->assertInstanceOf(Parser::class, $factory->createForHostVersion());
    }
}
