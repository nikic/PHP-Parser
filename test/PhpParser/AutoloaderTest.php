<?php declare(strict_types=1);

namespace PhpParser;

/* The autoloader is already active at this point, so we only check effects here. */

use PHPUnit\Framework\TestCase;

class AutoloaderTest extends TestCase
{
    public function testClassExists() {
        $this->assertTrue(class_exists(\PhpParser\NodeVisitorAbstract::class));
        $this->assertFalse(class_exists(\PHPParser_NodeVisitor_NameResolver::class));

        $this->assertFalse(class_exists(\PhpParser\FooBar::class));
        $this->assertFalse(class_exists(\PHPParser_FooBar::class));
    }
}
