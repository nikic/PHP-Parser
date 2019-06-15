<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

final class NumericLiteralSeparatorTokenEmulator implements TokenEmulatorInterface
{
    /**
     * @return int|null
     */
    public function getTokenId()
    {
        return null;
    }

    /**
     * @return int|null
     */
    public function getParserTokenId()
    {
        return null;
    }

    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return strpos($code, '_') !== false;
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

            $nextToken = $tokens[$i + 1];
            if (in_array($tokens[$i][0], [T_LNUMBER, T_DNUMBER], true) && $nextToken[0] === T_STRING && strpos($nextToken[1], '_') === 0) {
                $numericVault = $tokens[$i][1] . ltrim($nextToken[1], '_');

                // merge this and next token
                array_splice($tokens, $i, 2, [
                    [$tokens[$i][0], $numericVault, $line]
                ]);

                $c--;
                continue;
            }

            if (is_array($tokens[$i])) {
                $line += substr_count($tokens[$i][1], "\n");
            }
        }

        return $tokens;
    }
}
