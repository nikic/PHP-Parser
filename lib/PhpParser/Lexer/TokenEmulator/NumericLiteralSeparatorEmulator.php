<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

final class NumericLiteralSeparatorEmulator
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

            $nextToken = $tokens[$i + 1];
            if (in_array($tokens[$i][0], [T_LNUMBER, T_DNUMBER], true) && $nextToken[0] === T_STRING && strpos($nextToken[1], '_') === 0) {
                $numberOfTokensToSquash = 2;

                $numericVault = $tokens[$i][1];

                $isFloat = $tokens[$i][0] === T_DNUMBER;

                $nextPosition = $i + 1;
                while (isset($tokens[$nextPosition]) && $this->isPartOfNumberToken($tokens[$nextPosition])) {
                    $numericVault .= $tokens[$nextPosition][1];

                    if ($tokens[$nextPosition][0] === T_DNUMBER) {
                        $isFloat = true;
                    }

                    ++$nextPosition;
                    ++$numberOfTokensToSquash;
                }

                // merge this and next token
                array_splice($tokens, $i, $numberOfTokensToSquash, [
                    [$isFloat ? T_DNUMBER : T_LNUMBER, $numericVault, $line]
                ]);

                $c -= $numberOfTokensToSquash;
                continue;
            }

            if (is_array($tokens[$i])) {
                $line += substr_count($tokens[$i][1], "\n");
            }
        }

        return $tokens;
    }

    /**
     * @param mixed[] $token
     */
    private function isPartOfNumberToken(array $token): bool
    {
        if ($token[0] === T_STRING && strpos($token[1], '_') === 0) {
            return true;
        }

        if ($token[0] === T_DNUMBER) {
            return true;
        }

        return false;
    }
}
