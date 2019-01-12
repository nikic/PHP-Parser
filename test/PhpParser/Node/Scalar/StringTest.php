<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

class StringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideTestParseEscapeSequences
     */
    public function testParseEscapeSequences($expected, $string, $quote) {
        $this->assertSame(
            $expected,
            String_::parseEscapeSequences($string, $quote)
        );
    }

    /**
     * @dataProvider provideTestParse
     */
    public function testCreate($expected, $string) {
        $this->assertSame(
            $expected,
            String_::parse($string)
        );
    }

    public function provideTestParseEscapeSequences(): \Iterator
    {
        yield ['"',              '\\"',              '"'];
        yield ['\\"',            '\\"',              '`'];
        yield ['\\"\\`',         '\\"\\`',           null];
        yield ["\\\$\n\r\t\f\v", '\\\\\$\n\r\t\f\v', null];
        yield ["\x1B",           '\e',               null];
        yield [chr(255),         '\xFF',             null];
        yield [chr(255),         '\377',             null];
        yield [chr(0),           '\400',             null];
        yield ["\0",             '\0',               null];
        yield ['\xFF',           '\\\\xFF',          null];
    }

    public function provideTestParse() {
        $tests = [
            ['A', '\'A\''],
            ['A', 'b\'A\''],
            ['A', '"A"'],
            ['A', 'b"A"'],
            ['\\', '\'\\\\\''],
            ['\'', '\'\\\'\''],
        ];

        foreach ($this->provideTestParseEscapeSequences() as $i => $test) {
            // skip second and third tests, they aren't for double quotes
            if ($i !== 1 && $i !== 2) {
                $tests[] = [$test[0], '"' . $test[1] . '"'];
            }
        }

        return $tests;
    }
}
