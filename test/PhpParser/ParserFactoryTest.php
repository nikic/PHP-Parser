<?php declare(strict_types=1);

namespace PhpParser;

/* This test is very weak, because PHPUnit's assertEquals assertion is way too slow dealing with the
 * large objects involved here. So we just do some basic instanceof tests instead. */

use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;

class ParserFactoryTest extends \PHPUnit\Framework\TestCase {
    public function testCreate() {
        $factory = new ParserFactory();

        $lexer = new Lexer();
        $this->assertInstanceOf(Php7::class, $factory->create(ParserFactory::PREFER_PHP7, $lexer));
        $this->assertInstanceOf(Php7::class, $factory->create(ParserFactory::ONLY_PHP7, $lexer));
        $this->assertInstanceOf(Php7::class, $factory->create(ParserFactory::PREFER_PHP7));
        $this->assertInstanceOf(Php7::class, $factory->create(ParserFactory::ONLY_PHP7));

        $this->assertInstanceOf(Php8::class, $factory->createForNewestSupportedVersion());
        $this->assertInstanceOf(Parser::class, $factory->createForHostVersion());
    }
}
