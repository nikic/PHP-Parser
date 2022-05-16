<?php

declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Stmt\Echo_;
use PhpParser\ParserFactory;

final class EncapsedStringPartTest extends \PHPUnit\Framework\TestCase
{
    public function testRawValue()
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $nodes = $parser->parse('<?php echo "$value some \\"";');

        $echo = $nodes[0];
        $this->assertInstanceOf(Echo_::class, $echo);

        /** @var Echo_ $echoExprs */
        $echoExprs = $echo->exprs;

        $this->assertCount(1, $echoExprs);

        $firstEchoExprs = $echoExprs[0];
        $this->assertInstanceOf(Encapsed::class, $firstEchoExprs);

        /** @var Encapsed $firstEchoExprs */
        $this->assertCount(2, $firstEchoExprs->parts);

        $secondEncapsedPart = $firstEchoExprs->parts[1];
        $this->assertInstanceOf(EncapsedStringPart::class, $secondEncapsedPart);

        $this->assertSame( ' some "', $secondEncapsedPart->value);
        $this->assertSame(' some \\"', $secondEncapsedPart->getAttribute('rawValue'));
    }
}
