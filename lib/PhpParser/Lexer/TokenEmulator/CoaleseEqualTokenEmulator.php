<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Tokens;
use PhpParser\Token;

final class CoaleseEqualTokenEmulator implements TokenEmulatorInterface
{
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
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if (isset($tokens[$i + 1])) {
                $token = $tokens[$i];
                if ($token->id === Tokens::T_COALESCE && $tokens[$i + 1]->value === '=') {
                    array_splice($tokens, $i, 2, [
                        new Token(Tokens::T_COALESCE_EQUAL, '??=', $token->line, $token->filePos),
                    ]);
                    $c--;
                    continue;
                }
            }
        }

        return $tokens;
    }
}
