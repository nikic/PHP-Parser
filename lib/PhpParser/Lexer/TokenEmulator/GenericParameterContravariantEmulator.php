<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Tokens;

final class GenericParameterContravariantEmulator extends KeywordEmulator
{
    public function getPhpVersion(): string
    {
        return Emulative::PHP_8_1;
    }

    public function getKeywordString(): string
    {
        return 'out';
    }

    public function getKeywordToken(): int
    {
        return Tokens::T_GENERIC_PARAMETER_CONTRAVARIANT;
    }

    protected function isKeywordContext(array $tokens, int $pos): bool
    {
        return parent::isKeywordContext($tokens, $pos)
               && isset($tokens[$pos + 2])
               && $tokens[$pos + 1][0] === \T_WHITESPACE
               && $tokens[$pos + 2][0] === \T_STRING;
    }
}
