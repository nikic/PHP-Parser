<?php declare(strict_types=1);

namespace PhpParser;

class TokenTest extends \PHPUnit\Framework\TestCase {
    public function testGetTokenName(): void {
        $token = new Token(\ord(','), ',');
        $this->assertSame(',', $token->getTokenName());
        $token = new Token(\T_WHITESPACE, ' ');
        $this->assertSame('T_WHITESPACE', $token->getTokenName());
    }

    public function testIs(): void {
        $token = new Token(\ord(','), ',');
        $this->assertTrue($token->is(\ord(',')));
        $this->assertFalse($token->is(\ord(';')));
        $this->assertTrue($token->is(','));
        $this->assertFalse($token->is(';'));
        $this->assertTrue($token->is([\ord(','), \ord(';')]));
        $this->assertFalse($token->is([\ord('!'), \ord(';')]));
        $this->assertTrue($token->is([',', ';']));
        $this->assertFalse($token->is(['!', ';']));
    }

    /** @dataProvider provideTestIsIgnorable */
    public function testIsIgnorable(int $id, string $text, bool $isIgnorable): void {
        $token = new Token($id, $text);
        $this->assertSame($isIgnorable, $token->isIgnorable());
    }

    public static function provideTestIsIgnorable() {
        return [
            [\T_STRING, 'foo', false],
            [\T_WHITESPACE, ' ', true],
            [\T_COMMENT, '// foo', true],
            [\T_DOC_COMMENT, '/** foo */', true],
            [\T_OPEN_TAG, '<?php ', true],
        ];
    }

    public function testToString(): void {
        $token = new Token(\ord(','), ',');
        $this->assertSame(',', (string) $token);
        $token = new Token(\T_STRING, 'foo');
        $this->assertSame('foo', (string) $token);
    }
}
