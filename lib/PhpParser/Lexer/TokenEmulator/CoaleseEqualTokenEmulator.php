<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Tokens;

final class CoaleseEqualTokenEmulator implements TokenEmulatorInterface
{
    const T_COALESCE_EQUAL = 1007;

    public function getTokenId(): int
    {
        return self::T_COALESCE_EQUAL;
    }

    public function getParserTokenId(): int
    {
        return Tokens::T_COALESCE_EQUAL;
    }

    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return strpos($code, '??=') !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        $line = 1;
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if (isset($tokens[$i + 1])) {
                if ($tokens[$i][0] === T_COALESCE && $tokens[$i + 1] === '=') {
                    array_splice($tokens, $i, 2, [
                        [self::T_COALESCE_EQUAL, '??=', $line]
                    ]);
                    $c--;
                    continue;
                }
            }
            if (\is_array($tokens[$i])) {
                $line += substr_count($tokens[$i][1], "\n");
            }
        }

        return $tokens;
    }
}
