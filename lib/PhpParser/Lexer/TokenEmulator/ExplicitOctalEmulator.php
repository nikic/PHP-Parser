<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\Emulative;

class ExplicitOctalEmulator extends TokenEmulator {
    public function getPhpVersion(): string {
        return Emulative::PHP_8_1;
    }

    public function isEmulationNeeded(string $code): bool {
        return strpos($code, '0o') !== false || strpos($code, '0O') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if ($tokens[$i][0] == \T_LNUMBER && $tokens[$i][1] === '0' &&
                isset($tokens[$i + 1]) && $tokens[$i + 1][0] == \T_STRING &&
                preg_match('/[oO][0-7]+(?:_[0-7]+)*/', $tokens[$i + 1][1])
            ) {
                $tokenKind = $this->resolveIntegerOrFloatToken($tokens[$i + 1][1]);
                array_splice($tokens, $i, 2, [
                    [$tokenKind, '0' . $tokens[$i + 1][1], $tokens[$i][2]],
                ]);
                $c--;
            }
        }
        return $tokens;
    }

    private function resolveIntegerOrFloatToken(string $str): int
    {
        $str = substr($str, 1);
        $str = str_replace('_', '', $str);
        $num = octdec($str);
        return is_float($num) ? \T_DNUMBER : \T_LNUMBER;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        // Explicit octals were not legal code previously, don't bother.
        return $tokens;
    }
}