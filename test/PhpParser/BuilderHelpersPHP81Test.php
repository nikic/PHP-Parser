<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;

class BuilderHelpersPHP81Test extends \PHPUnit\Framework\TestCase
{
    public function testNormalizeValueEnum() {
        if (\PHP_VERSION_ID <= 80100) {
            $this->markTestSkipped('Enums are supported since PHP 8.1');
        }

        include __DIR__ . '/../code/Suit.php';

        $this->assertEquals(new Expr\ClassConstFetch(new FullyQualified(\Suit::class), new Identifier('Hearts')), BuilderHelpers::normalizeValue(\Suit::Hearts));
    }
}
