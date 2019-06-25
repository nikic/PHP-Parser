<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

final class NumericLiteralSeparatorEmulator implements TokenEmulatorInterface
{
    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return preg_match('~[0-9a-f]_[0-9a-f]~i', $code) !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        $line = 1;
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if (!isset($tokens[$i + 1])) {
                continue;
            }

            $token = $tokens[$i];
            $nextToken = $tokens[$i + 1];
            if (
                $this->isNumberToken($token) && $nextToken[0] === T_STRING && strpos($nextToken[1], '_') === 0
            ) {
                $numberOfTokensToSquash = 1;

                $numericValue = $token[1];

                $nextPosition = $i + 1;

                while (isset($tokens[$nextPosition]) && $this->isPartOfNumberToken($tokens[$nextPosition], $tokens[$nextPosition - 1])) {
                    $nextToken = $tokens[$nextPosition];

                    $numericValue .= is_string($nextToken) ? $nextToken : $nextToken[1];

                    ++$nextPosition;
                    ++$numberOfTokensToSquash;
                }

                $tokenKind = $this->resolveIntegerOrFloatToken($numericValue);
                array_splice($tokens, $i, $numberOfTokensToSquash, [
                    [$tokenKind, $numericValue, $line]
                ]);

                $c -= $numberOfTokensToSquash - 1;
                continue;
            }

            if (is_array($token)) {
                $line += substr_count($token[1], "\n");
            }
        }

        return $tokens;
    }

    /**
     * @param mixed[]|string $token
     * @param mixed[]|string $previousToken
     */
    private function isPartOfNumberToken($token, $previousToken): bool
    {
        if (is_array($token)) {
            if ($token[0] === T_STRING && strpos($token[1], '_') === 0) {
                return true;
            }

            if ($token[0] === T_LNUMBER) {
                return true;
            }

            if ($token[0] === T_DNUMBER) {
                return true;
            }

        } else {
            // matches cases like "1_0e+10" - @todo actually skips first token, because it's a string
            if ($token === '+' || $token === '-') {
                if ($previousToken[0] === T_STRING && $previousToken[1][0] === '_') {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    private function resolveIntegerOrFloatToken(string $numericValue): int
    {
        $numericValueWithoutUnderscores = str_replace('_', '', $numericValue);

        if (stripos($numericValueWithoutUnderscores, '0b') === 0) {
            $decimalForm = bindec($numericValueWithoutUnderscores);
        } elseif (stripos($numericValueWithoutUnderscores, '0x') === 0) {
            $decimalForm = hexdec($numericValueWithoutUnderscores);
        } elseif (stripos($numericValueWithoutUnderscores, '0') === 0 && ctype_digit(($numericValueWithoutUnderscores))) {
            $decimalForm = octdec($numericValueWithoutUnderscores);
        } else {
            $decimalForm = +$numericValueWithoutUnderscores;
        }

        return is_float($decimalForm) ? T_DNUMBER : T_LNUMBER;
    }

    /**
     * @param array|string $token
     */
    private function isNumberToken($token): bool
    {
        if (! is_array($token)) {
            return false;
        }

        return $token[0] === T_LNUMBER || $token[0] === T_DNUMBER;
    }
}
