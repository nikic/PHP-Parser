<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

final class NullsafeTokenEmulator implements TokenEmulatorInterface
{
    public function getPhpVersion(): string
    {
        return Emulative::PHP_8_0;
    }

    public function isEmulationNeeded(string $code): bool
    {
        return strpos($code, '?->') !== false;
    }

    public function emulate(string $code, array $tokens): array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        $line = 1;
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if (isset($tokens[$i + 1])) {
                if ($tokens[$i] === '?' && $tokens[$i + 1][0] === \T_OBJECT_OPERATOR) {
                    array_splice($tokens, $i, 2, [
                        [\T_NULLSAFE_OBJECT_OPERATOR, '?->', $line]
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

    public function reverseEmulate(string $code, array $tokens): array
    {
        // ?-> was not valid code previously, don't bother.
        return $tokens;
    }
}
