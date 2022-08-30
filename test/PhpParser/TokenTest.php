<?php declare(strict_types=1);

namespace PhpParser;

class TokenTest extends \PHPUnit\Framework\TestCase {
    /**
     * @requires PHP >= 8.0
     */
    public function testTokenize() {
        $code = file_get_contents(__FILE__);
        $tokens = \PhpToken::tokenize($code);
        $polyfillTokens = Token::tokenize($code);
        $this->assertEqualsCanonicalizing((array) $tokens[1], (array) $polyfillTokens[1]);
        $this->assertEqualsCanonicalizing((array) $tokens[50], (array) $polyfillTokens[50]);
        $this->assertEqualsCanonicalizing((array) $tokens[100], (array) $polyfillTokens[100]);
        $this->assertEqualsCanonicalizing((array) $tokens[150], (array) $polyfillTokens[150]);
        $this->assertEqualsCanonicalizing((array) $tokens[200], (array) $polyfillTokens[200]);
        $this->assertEqualsCanonicalizing((array) $tokens[250], (array) $polyfillTokens[250]);
    }

    public function testGetTokenName() {
        $token = new Token(\ord(','), ',');
        $this->assertSame(',', $token->getTokenName());
        $token = new Token(\T_WHITESPACE, ' ');
        $this->assertSame('T_WHITESPACE', $token->getTokenName());
    }

    /**
     * @covers Token::getTokenName
     * @requires PHP >= 8.0
     */
    public function testGetTokenNameVsNativeMethod() {
        // named tokens
        $token = new \PhpToken(\T_ECHO, 'echo');
        $polyfillToken = new Token(\T_ECHO, 'echo');
        $this->assertSame($token->getTokenName(), $polyfillToken->getTokenName());
        // single char tokens
        $token = new \PhpToken(\ord(';'), ';');
        $polyfillToken = new Token(\ord(';'), ';');
        $this->assertSame($token->getTokenName(), $polyfillToken->getTokenName());
        // unknown token
        $token = new \PhpToken(10000, "\0");
        $polyfillToken = new Token(10000, "\0");
        $this->assertSame($token->getTokenName(), $polyfillToken->getTokenName());
    }

    public function testIs() {
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

    /**
     * @covers Token::is
     * @requires PHP >= 8.0
     */
    public function testIsVsNativeMethod() {
        // single token
        $token = new \PhpToken(\T_ECHO, 'echo');
        $polyfillToken = new Token(\T_ECHO, 'echo');
        $this->assertSame($token->is(\T_ECHO), $polyfillToken->is(\T_ECHO));
        $this->assertSame($token->is('echo'), $polyfillToken->is('echo'));
        $this->assertSame($token->is('T_ECHO'), $polyfillToken->is('T_ECHO'));
        // token set
        $token = new \PhpToken(\T_TRAIT, 'trait');
        $polyfillToken = new Token(\T_TRAIT, 'trait');
        $this->assertSame(
            $token->is([\T_INTERFACE, \T_CLASS, \T_TRAIT]),
            $polyfillToken->is([\T_INTERFACE, \T_CLASS, \T_TRAIT])
        );
        // mixed set
        $token = new \PhpToken(\T_TRAIT, 'trait');
        $polyfillToken = new Token(\T_TRAIT, 'trait');
        $this->assertSame(
            $token->is([\T_INTERFACE, 'class', 334]),
            $polyfillToken->is([\T_INTERFACE, 'class', 334])
        );
    }

    /** @dataProvider provideTestIsIgnorable */
    public function testIsIgnorable(int $id, string $text, bool $isIgnorable) {
        $token = new Token($id, $text);
        $this->assertSame($isIgnorable, $token->isIgnorable());
    }

    public function provideTestIsIgnorable() {
        return [
            [\T_STRING, 'foo', false],
            [\T_CLOSE_TAG, '?>', false],
            [\T_OPEN_TAG_WITH_ECHO, '<?=', false],
            [\T_INLINE_HTML, 'inline <b>HTML</b>', false],
            [\T_WHITESPACE, ' ', true],
            [\T_COMMENT, '// foo', true],
            [\T_DOC_COMMENT, '/** foo */', true],
            [\T_OPEN_TAG, '<?php ', true],
        ];
    }

    public function testToString() {
        $token = new Token(\ord(','), ',');
        $this->assertSame(',', (string) $token);
        $token = new Token(\T_STRING, 'foo');
        $this->assertSame('foo', (string) $token);
    }
}
