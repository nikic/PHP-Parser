<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Stmt\Echo_;
use PhpParser\ParserFactory;

class NumberTest extends \PHPUnit\Framework\TestCase {
    public function testRawValue(): void {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $nodes = $parser->parse('<?php echo 1_234;');

        $echo = $nodes[0];
        $this->assertInstanceOf(Echo_::class, $echo);

        /** @var Echo_ $echo */
        $lnumber = $echo->exprs[0];
        $this->assertInstanceOf(Int_::class, $lnumber);

        /** @var Int_ $lnumber */
        $this->assertSame(1234, $lnumber->value);
        $this->assertSame('1_234', $lnumber->getAttribute('rawValue'));
    }
}
