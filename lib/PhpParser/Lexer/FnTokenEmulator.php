<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\Parser\Tokens;

final class FnTokenEmulator implements TokenEmulatorInterface
{
    const T_FN = 1008;

    public function getTokenId(): int
    {
        return self::T_FN;
    }

    public function getParserTokenId(): int
    {
        return Tokens::T_FN;
    }

    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return strpos($code, 'fn') !== false;
    }

    public function emulate(string $code, array $tokens) : array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if ($tokens[$i][0] === T_STRING && $tokens[$i][1] === 'fn') {
                if (isset($tokens[$i - 1]) && $tokens[$i - 1][0] === T_OBJECT_OPERATOR) {
                    continue;
                }

                $tokens[$i][0] = self::T_FN;
            }
        }

        return $tokens;
    }
}
