<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Stmt\Echo_;
use PhpParser\ParserFactory;

class StringTest extends \PHPUnit\Framework\TestCase {
    public function testRawValue(): void {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $nodes = $parser->parse('<?php echo "sequence \x41";');

        $echo = $nodes[0];
        $this->assertInstanceOf(Echo_::class, $echo);

        /** @var Echo_ $echo */
        $string = $echo->exprs[0];
        $this->assertInstanceOf(String_::class, $string);

        /** @var String_ $string */
        $this->assertSame('sequence A', $string->value);
        $this->assertSame('"sequence \\x41"', $string->getAttribute('rawValue'));
    }

    /**
     * @dataProvider provideTestParseEscapeSequences
     */
    public function testParseEscapeSequences($expected, $string, $quote): void {
        $this->assertSame(
            $expected,
            String_::parseEscapeSequences($string, $quote)
        );
    }

    /**
     * @dataProvider provideTestParse
     */
    public function testCreate($expected, $string): void {
        $this->assertSame(
            $expected,
            String_::parse($string)
        );
    }

    public static function provideTestParseEscapeSequences() {
        return [
            ['"',              '\\"',              '"'],
            ['\\"',            '\\"',              '`'],
            ['\\"\\`',         '\\"\\`',           null],
            ["\\\$\n\r\t\f\v", '\\\\\$\n\r\t\f\v', null],
            ["\x1B",           '\e',               null],
            [chr(255),         '\xFF',             null],
            [chr(255),         '\377',             null],
            [chr(0),           '\400',             null],
            ["\0",             '\0',               null],
            ['\xFF',           '\\\\xFF',          null],
        ];
    }

    public static function provideTestParse() {
        $tests = [
            ['A', '\'A\''],
            ['A', 'b\'A\''],
            ['A', '"A"'],
            ['A', 'b"A"'],
            ['\\', '\'\\\\\''],
            ['\'', '\'\\\'\''],
        ];

        foreach (self::provideTestParseEscapeSequences() as $i => $test) {
            // skip second and third tests, they aren't for double quotes
            if ($i !== 1 && $i !== 2) {
                $tests[] = [$test[0], '"' . $test[1] . '"'];
            }
        }

        return $tests;
    }
}
