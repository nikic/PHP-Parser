<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Token;

final class FnTokenEmulator implements TokenEmulatorInterface
{
    public function isEmulationNeeded(string $code) : bool
    {
        // skip version where this is supported
        if (version_compare(\PHP_VERSION, Emulative::PHP_7_4, '>=')) {
            return false;
        }

        return strpos($code, 'fn') !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        foreach ($tokens as $i => $token) {
            if ($token->id === \T_STRING && $token->value === 'fn') {
                $previousNonSpaceToken = $this->getPreviousNonSpaceToken($tokens, $i);
                if ($previousNonSpaceToken !== null && $previousNonSpaceToken->id === T_OBJECT_OPERATOR) {
                    continue;
                }

                $token->id = Emulative::T_FN;
            }
        }

        return $tokens;
    }

    /**
     * @param Token[] $tokens
     * @return Token|null
     */
    private function getPreviousNonSpaceToken(array $tokens, int $start)
    {
        for ($i = $start - 1; $i >= 0; --$i) {
            if ($tokens[$i]->id === \T_WHITESPACE) {
                continue;
            }

            return $tokens[$i];
        }

        return null;
    }
}
