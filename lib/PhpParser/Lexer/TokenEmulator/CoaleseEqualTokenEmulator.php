<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Token;

final class CoaleseEqualTokenEmulator extends TokenEmulator
{
    public function getPhpVersion(): string
    {
        return Emulative::PHP_7_4;
    }

    public function isEmulationNeeded(string $code): bool
    {
        return strpos($code, '??=') !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if (isset($tokens[$i + 1])) {
                if ($token->id === T_COALESCE && $tokens[$i + 1]->text === '=') {
                    array_splice($tokens, $i, 2, [
                        new Token(\T_COALESCE_EQUAL, '??=', $token->line, $token->pos),
                    ]);
                    $c--;
                    continue;
                }
            }
        }

        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array
    {
        // ??= was not valid code previously, don't bother.
        return $tokens;
    }
}
