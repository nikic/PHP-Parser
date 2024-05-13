<?php
declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Stmt\Echo_;
use PhpParser\ParserFactory;

class DNumberTest extends \PHPUnit\Framework\TestCase {
    public function testRawValue(): void {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $nodes = $parser->parse('<?php echo 1_234.56;');

        $echo = $nodes[0];
        $this->assertInstanceOf(Echo_::class, $echo);

        /** @var Echo_ $echo */
        $lLumber = $echo->exprs[0];
        $this->assertInstanceOf(Float_::class, $lLumber);

        /** @var Float_ $dnumber */
        $this->assertSame(1234.56, $lLumber->value);
        $this->assertSame('1_234.56', $lLumber->getAttribute('rawValue'));
    }
}
