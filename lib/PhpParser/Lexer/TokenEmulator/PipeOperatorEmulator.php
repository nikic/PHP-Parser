<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\TokenEmulator\TokenEmulator;
use PhpParser\PhpVersion;
use PhpParser\Token;

class PipeOperatorEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 5);
    }

    public function isEmulationNeeded(string $code): bool {
        return \strpos($code, '|>') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->text !== '|' || !isset($tokens[$i + 1]) || !$this->isGreaterOrEqualRun($tokens[$i + 1]->text)
                || $tokens[$i + 1]->text[0] !== '>'
            ) {
                continue;
            }

            // The lexer of older versions may have merged the ">" of "|>" with the characters after it,
            // e.g. "|>=" is lexed as "|" and ">=". Lex the rest again, as PHP 8.5 does.
            $length = $this->getGreaterOrEqualRunLength($tokens, $i + 1);
            $text = $this->concatTokenTexts($tokens, $i + 1, $length);
            $replacement = \array_merge(
                [new Token(\T_PIPE, '|>', $token->line, $token->pos)],
                $this->relex(\substr($text, 1), $token->line, $token->pos + 2)
            );
            array_splice($tokens, $i, 1 + $length, $replacement);
            $c += \count($replacement) - 1 - $length;
        }
        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->id !== \T_PIPE) {
                continue;
            }

            // Older versions lex the ">" of "|>" together with the ">" and "=" characters after it,
            // e.g. "|>=" is lexed as "|" and ">=".
            $length = $this->getGreaterOrEqualRunLength($tokens, $i + 1);
            if ($length === 0) {
                array_splice($tokens, $i, 1, [
                    new Token(\ord('|'), '|', $token->line, $token->pos),
                    new Token(\ord('>'), '>', $token->line, $token->pos + 1),
                ]);
                ++$i;
                ++$c;
                continue;
            }

            $text = '>' . $this->concatTokenTexts($tokens, $i + 1, $length);
            $replacement = \array_merge(
                [new Token(\ord('|'), '|', $token->line, $token->pos)],
                $this->relex($text, $token->line, $token->pos + 1)
            );
            array_splice($tokens, $i, 1 + $length, $replacement);
            $c += \count($replacement) - 1 - $length;
            $i += \count($replacement) - 1;
        }
        return $tokens;
    }

    private function isGreaterOrEqualRun(string $text): bool {
        return $text !== '' && \strspn($text, '>=') === \strlen($text);
    }

    /**
     * @param Token[] $tokens
     */
    private function getGreaterOrEqualRunLength(array $tokens, int $start): int {
        $length = 0;
        while (isset($tokens[$start + $length]) && $this->isGreaterOrEqualRun($tokens[$start + $length]->text)) {
            $length++;
        }
        return $length;
    }

    /**
     * @param Token[] $tokens
     */
    private function concatTokenTexts(array $tokens, int $start, int $length): string {
        $text = '';
        for ($i = $start; $i < $start + $length; $i++) {
            $text .= $tokens[$i]->text;
        }
        return $text;
    }

    /**
     * Lex a run of ">" and "=" characters. These operators are lexed the same way by all PHP versions.
     *
     * @return Token[]
     */
    private function relex(string $text, int $line, int $pos): array {
        if ($text === '') {
            return [];
        }

        $tokens = [];
        foreach (\array_slice(Token::tokenize('<?php ' . $text), 1) as $token) {
            $tokens[] = new Token($token->id, $token->text, $line, $pos);
            $pos += \strlen($token->text);
        }
        return $tokens;
    }
}
