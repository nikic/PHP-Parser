<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Tokens;
use PhpParser\Token;

final class NumericLiteralSeparatorEmulator implements TokenEmulatorInterface
{
    const BIN = '(?:0b[01]+(?:_[01]+)*)';
    const HEX = '(?:0x[0-9a-f]+(?:_[0-9a-f]+)*)';
    const DEC = '(?:[0-9]+(?:_[0-9]+)*)';
    const SIMPLE_FLOAT = '(?:' . self::DEC . '\.' . self::DEC . '?|\.' . self::DEC . ')';
    const EXP = '(?:e[+-]?' . self::DEC . ')';
    const FLOAT = '(?:' . self::SIMPLE_FLOAT . self::EXP . '?|' . self::DEC . self::EXP . ')';
    const NUMBER = '~' . self::FLOAT . '|' . self::BIN . '|' . self::HEX . '|' . self::DEC . '~iA';

    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return preg_match('~[0-9a-f]_[0-9a-f]~i', $code) !== false;
    }

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            $tokenLen = \strlen($token->value);

            if ($token->id !== Tokens::T_LNUMBER && $token->id !== Tokens::T_DNUMBER) {
                continue;
            }

            $res = preg_match(self::NUMBER, $code, $matches, 0, $token->filePos);
            assert($res, "No number at number token position");

            $match = $matches[0];
            $matchLen = \strlen($match);
            if ($matchLen === $tokenLen) {
                // Original token already holds the full number.
                continue;
            }

            $tokenKind = $this->resolveIntegerOrFloatToken($match);
            $newTokens = [new Token($tokenKind, $match, $token->line, $token->filePos)];

            $numTokens = 1;
            $len = $tokenLen;
            while ($matchLen > $len) {
                $nextToken = $tokens[$i + $numTokens];
                $nextTokenLen = \strlen($nextToken->value);

                $numTokens++;
                if ($matchLen < $len + $nextTokenLen) {
                    // Split trailing characters into a partial token.
                    $partialText = substr($nextToken->value, $matchLen - $len);
                    $newTokens[] = new Token(
                        $nextToken->id, $partialText, $nextToken->line, $nextToken->filePos
                    );
                    break;
                }

                $len += $nextTokenLen;
            }

            array_splice($tokens, $i, $numTokens, $newTokens);
            $c -= $numTokens - \count($newTokens);
        }

        return $tokens;
    }

    private function resolveIntegerOrFloatToken(string $str): int
    {
        $str = str_replace('_', '', $str);

        if (stripos($str, '0b') === 0) {
            $num = bindec($str);
        } elseif (stripos($str, '0x') === 0) {
            $num = hexdec($str);
        } elseif (stripos($str, '0') === 0 && ctype_digit($str)) {
            $num = octdec($str);
        } else {
            $num = +$str;
        }

        return is_float($num) ? Tokens::T_DNUMBER : Tokens::T_LNUMBER;
    }
}
