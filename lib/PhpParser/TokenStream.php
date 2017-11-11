<?php declare(strict_types=1);

namespace PhpParser;

/**
 * Provides operations on token streams, for use by pretty printer.
 *
 * @internal
 */
class TokenStream {
    /** @var array Tokens (in token_get_all format) */
    private $tokens;
    /** @var int[] Map from position to indentation */
    private $indentMap;

    /**
     * Create token stream instance.
     *
     * @param array $tokens Tokens in token_get_all() format
     */
    public function __construct(array $tokens) {
        $this->tokens = $tokens;
        $this->indentMap = $this->calcIndentMap();
    }

    /**
     * Whether the given position is immediately surrounded by parenthesis.
     *
     * @param int $startPos Start position
     * @param int $endPos   End position
     */
    public function haveParens(int $startPos, int $endPos) : bool {
        return $this->haveTokenImmediativelyBefore($startPos, '(')
            && $this->haveTokenImmediatelyAfter($endPos, ')');
    }

    /**
     * Whether the given position is immediately surrounded by braces.
     *
     * @param int $startPos Start position
     * @param int $endPos   End position
     */
    public function haveBraces(int $startPos, int $endPos) : bool {
        return $this->haveTokenImmediativelyBefore($startPos, '{')
            && $this->haveTokenImmediatelyAfter($endPos, '}');
    }

    /**
     * Check whether the position is directly preceded by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int        $pos               Position before which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    public function haveTokenImmediativelyBefore(int $pos, $expectedTokenType) : bool {
        $tokens = $this->tokens;
        $pos--;
        for (; $pos >= 0; $pos--) {
            $tokenType = $tokens[$pos][0];
            if ($tokenType === $expectedTokenType) {
                return true;
            }
            if ($tokenType !== \T_WHITESPACE
                && $tokenType !== \T_COMMENT && $tokenType !== \T_DOC_COMMENT) {
                break;
            }
        }
        return false;
    }

    /**
     * Check whether the position is directly followed by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int        $pos               Position after which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    public function haveTokenImmediatelyAfter(int $pos, $expectedTokenType) : bool {
        $tokens = $this->tokens;
        $pos++;
        for (; $pos < \count($tokens); $pos++) {
            $tokenType = $tokens[$pos][0];
            if ($tokenType === $expectedTokenType) {
                return true;
            }
            if ($tokenType !== \T_WHITESPACE
                && $tokenType !== \T_COMMENT && $tokenType !== \T_DOC_COMMENT) {
                break;
            }
        }
        return false;
    }

    public function skipLeft(int $pos, $skipTokenType) {
        $tokens = $this->tokens;

        $pos = $this->skipLeftWhitespace($pos);
        if ($skipTokenType === \T_WHITESPACE) {
            return $pos;
        }

        if ($tokens[$pos][0] !== $skipTokenType) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos--;

        return $this->skipLeftWhitespace($pos);
    }

    public function skipRight(int $pos, $skipTokenType) {
        $tokens = $this->tokens;

        $pos = $this->skipRightWhitespace($pos);
        if ($skipTokenType === \T_WHITESPACE) {
            return $pos;
        }

        if ($tokens[$pos][0] !== $skipTokenType) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos++;

        return $this->skipRightWhitespace($pos);
    }

    /**
     * Return first non-whitespace token position smaller or equal to passed position.
     *
     * @param int $pos Token position
     * @return int Non-whitespace token position
     */
    public function skipLeftWhitespace(int $pos) {
        $tokens = $this->tokens;
        for (; $pos >= 0; $pos--) {
            $type = $tokens[$pos][0];
            if ($type !== \T_WHITESPACE && $type !== \T_COMMENT && $type !== \T_DOC_COMMENT) {
                break;
            }
        }
        return $pos;
    }

    /**
     * Return first non-whitespace position greater or equal to passed position.
     *
     * @param int $pos Token position
     * @return int Non-whitespace token position
     */
    public function skipRightWhitespace(int $pos) {
        $tokens = $this->tokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            $type = $tokens[$pos][0];
            if ($type !== \T_WHITESPACE && $type !== \T_COMMENT && $type !== \T_DOC_COMMENT) {
                break;
            }
        }
        return $pos;
    }

    public function findRight($pos, $findTokenType) {
        $tokens = $this->tokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            $type = $tokens[$pos][0];
            if ($type === $findTokenType) {
                return $pos;
            }
        }
        return -1;
    }

    /**
     * Get indentation before token position.
     *
     * @param int $pos Token position
     *
     * @return int Indentation depth (in spaces)
     */
    public function getIndentationBefore(int $pos) : int {
        return $this->indentMap[$pos];
    }

    /**
     * Get the code corresponding to a token offset range, optionally adjusted for indentation.
     *
     * @param int $from   Token start position (inclusive)
     * @param int $to     Token end position (exclusive)
     * @param int $indent By how much the code should be indented (can be negative as well)
     *
     * @return string Code corresponding to token range, adjusted for indentation
     */
    public function getTokenCode(int $from, int $to, int $indent) : string {
        $tokens = $this->tokens;
        $result = '';
        for ($pos = $from; $pos < $to; $pos++) {
            $token = $tokens[$pos];
            if (\is_array($token)) {
                $type = $token[0];
                $content = $token[1];
                if ($type === \T_CONSTANT_ENCAPSED_STRING || $type === \T_ENCAPSED_AND_WHITESPACE) {
                    $result .= $content;
                } else {
                    // TODO Handle non-space indentation
                    if ($indent < 0) {
                        $result .= str_replace("\n" . str_repeat(" ", -$indent), "\n", $content);
                    } else if ($indent > 0) {
                        $result .= str_replace("\n", "\n" . str_repeat(" ", $indent), $content);
                    } else {
                        $result .= $content;
                    }
                }
            } else {
                $result .= $token;
            }
        }
        return $result;
    }

    /**
     * Precalculate the indentation at every token position.
     *
     * @return int[] Token position to indentation map
     */
    private function calcIndentMap() {
        $indentMap = [];
        $indent = 0;
        foreach ($this->tokens as $token) {
            $indentMap[] = $indent;

            if ($token[0] === \T_WHITESPACE) {
                $content = $token[1];
                $newlinePos = \strrpos($content, "\n");
                if (false !== $newlinePos) {
                    $indent = \strlen($content) - $newlinePos - 1;
                }
            }
        }

        // Add a sentinel for one past end of the file
        $indentMap[] = $indent;

        return $indentMap;
    }
}