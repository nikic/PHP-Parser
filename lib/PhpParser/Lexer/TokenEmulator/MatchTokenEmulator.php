<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

final class MatchTokenEmulator implements TokenEmulatorInterface
{
    public function getPhpVersion(): string
    {
        return Emulative::PHP_8_0;
    }

    public function isEmulationNeeded(string $code): bool
    {
        return strpos($code, 'match') !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        foreach ($tokens as $i => $token) {
            if ($token[0] === T_STRING && strtolower($token[1]) === 'match') {
                $previousNonSpaceToken = $this->getPreviousNonSpaceToken($tokens, $i);
                if ($previousNonSpaceToken !== null && $previousNonSpaceToken[0] === T_OBJECT_OPERATOR) {
                    continue;
                }

                $tokens[$i][0] = Emulative::T_MATCH;
            }
        }

        return $tokens;
    }

    /**
     * @param mixed[] $tokens
     * @return mixed[]|null
     */
    private function getPreviousNonSpaceToken(array $tokens, int $start)
    {
        for ($i = $start - 1; $i >= 0; --$i) {
            if ($tokens[$i][0] === T_WHITESPACE) {
                continue;
            }

            return $tokens[$i];
        }

        return null;
    }
}
