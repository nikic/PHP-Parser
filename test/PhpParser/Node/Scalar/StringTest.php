<?php

namespace PhpParser\Node\Scalar;

use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
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

    public function provideTestParseEscapeSequences() {
        return array(
            array('"',              '\\"',              '"'),
            array('\\"',            '\\"',              '`'),
            array('\\"\\`',         '\\"\\`',           null),
            array("\\\$\n\r\t\f\v", '\\\\\$\n\r\t\f\v', null),
            array("\x1B",           '\e',               null),
            array(chr(255),         '\xFF',             null),
            array(chr(255),         '\377',             null),
            array(chr(0),           '\400',             null),
            array("\0",             '\0',               null),
            array('\xFF',           '\\\\xFF',          null),
        );
    }

    public function provideTestParse() {
        $tests = array(
            array('A', '\'A\''),
            array('A', 'b\'A\''),
            array('A', '"A"'),
            array('A', 'b"A"'),
            array('\\', '\'\\\\\''),
            array('\'', '\'\\\'\''),
        );

        foreach ($this->provideTestParseEscapeSequences() as $i => $test) {
            // skip second and third tests, they aren't for double quotes
            if ($i != 1 && $i != 2) {
                $tests[] = array($test[0], '"' . $test[1] . '"');
            }
        }

        return $tests;
    }
}
