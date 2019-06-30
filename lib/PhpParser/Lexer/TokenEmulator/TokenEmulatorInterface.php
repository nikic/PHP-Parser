<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Token;

/** @internal */
interface TokenEmulatorInterface
{
    public function isEmulationNeeded(string $code): bool;

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    public function emulate(string $code, array $tokens): array;
}
