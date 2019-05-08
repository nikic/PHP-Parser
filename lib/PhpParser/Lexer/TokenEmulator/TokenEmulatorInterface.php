<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

interface TokenEmulatorInterface
{
    public function getTokenId(): int;

    public function getParserTokenId(): int;

    public function isEmulationNeeded(string $code): bool;

    /**
     * @return array Modified Tokens
     */
    public function emulate(string $code, array $tokens): array;
}
