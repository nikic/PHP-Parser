<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Token;

abstract class KeywordEmulator extends TokenEmulator
{
    abstract function getKeywordString(): string;
    abstract function getKeywordToken(): int;

    final public function isEmulationNeeded(string $code): bool
    {
        return strpos(strtolower($code), $this->getKeywordString()) !== false;
    }

    /** @param Token[] $tokens */
    protected function isKeywordContext(array $tokens, int $pos): bool
    {
        $previousNonSpaceToken = $this->getPreviousNonSpaceToken($tokens, $pos);
        return $previousNonSpaceToken === null || $previousNonSpaceToken->id !== \T_OBJECT_OPERATOR;
    }

    final public function emulate(string $code, array $tokens): array
    {
        $keywordString = $this->getKeywordString();
        foreach ($tokens as $i => $token) {
            if ($token->id === T_STRING && strtolower($token->text) === $keywordString
                    && $this->isKeywordContext($tokens, $i)) {
                $token->id = $this->getKeywordToken();
            }
        }

        return $tokens;
    }

    /** @param Token[] $tokens */
    private function getPreviousNonSpaceToken(array $tokens, int $start): ?Token
    {
        for ($i = $start - 1; $i >= 0; --$i) {
            if ($tokens[$i]->id === T_WHITESPACE) {
                continue;
            }

            return $tokens[$i];
        }

        return null;
    }

    final public function reverseEmulate(string $code, array $tokens): array
    {
        $keywordToken = $this->getKeywordToken();
        foreach ($tokens as $i => $token) {
            if ($token->id === $keywordToken) {
                $token->id = \T_STRING;
            }
        }

        return $tokens;
    }
}
