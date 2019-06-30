<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Tokens;

class Lexer
{
    /* Token ID used for illegal characters part of the token stream. These are dropped by
     * token_get_all(), but we restore them here to make sure that the tokens cover the full
     * original text, and to prevent file positions from going out of sync. */
    const T_BAD_CHARACTER = -1;

    /** @var array Map from PHP tokens to PhpParser tokens. */
    protected $tokenMap;

    /**
     * Creates a Lexer.
     *
     * @param array $options Options array. Currently unused.
     */
    public function __construct(array $options = []) {
        $this->tokenMap = $this->createTokenMap();
    }

    /**
     * Retrieve the token map.
     *
     * The token maps PHP tokens into PhpParser tokens. Tokens that should be discarded
     * by the parser are mapped to null.
     *
     * @return array
     */
    public function getTokenMap(): array {
        return $this->tokenMap;
    }

    /**
     * Tokenizes the given PHP code into an array of Tokens.
     *
     * This function does not throw if lexing errors occur. Instead, errors may be retrieved using
     * the getErrors() method.
     *
     * @param string $code The source code to tokenize
     * @param ErrorHandler|null $errorHandler Error handler to use for lexing errors. Defaults to
     *                                        ErrorHandler\Throwing
     *
     * @return Token[] Sequence of tokens
     */
    public function tokenize(string $code, ErrorHandler $errorHandler = null) {
        if (null === $errorHandler) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        $scream = ini_set('xdebug.scream', '0');
        error_clear_last();
        $rawTokens = @token_get_all($code);
        $checkForMissingTokens = null !== error_get_last();
        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }

        $tokens = [];
        $filePos = 0;
        $line = 1;
        foreach ($rawTokens as $rawToken) {
            if (\is_array($rawToken)) {
                $token = new Token($rawToken[0], $rawToken[1], $line, $filePos);
            } elseif (\strlen($rawToken) == 2) {
                // Bug in token_get_all() when lexing b".
                $token = new Token(\ord('"'), $rawToken, $line, $filePos);
            } else {
                $token = new Token(\ord($rawToken), $rawToken, $line, $filePos);
            }

            $value = $token->value;
            $tokenLen = \strlen($value);
            if ($checkForMissingTokens && substr($code, $filePos, $tokenLen) !== $value) {
                // Something is missing, must be an invalid character
                $nextFilePos = strpos($code, $value, $filePos);
                $badCharTokens = $this->handleInvalidCharacterRange(
                    $code, $filePos, $nextFilePos, $line, $errorHandler);
                $tokens = array_merge($tokens, $badCharTokens);
                $filePos = (int) $nextFilePos;
            }

            $tokens[] = $token;
            $filePos += $tokenLen;
            $line += substr_count($value, "\n");
        }

        if ($filePos !== \strlen($code)) {
            // Invalid characters at the end of the input
            $badCharTokens = $this->handleInvalidCharacterRange(
                $code, $filePos, \strlen($code), $line, $errorHandler);
            $tokens = array_merge($tokens, $badCharTokens);
        }

        if (\count($tokens) > 0) {
            // Check for unterminated comment
            $lastToken = $tokens[\count($tokens) - 1];
            if ($this->isUnterminatedComment($lastToken)) {
                $errorHandler->handleError(new Error('Unterminated comment', [
                    'startLine' => $line - substr_count($lastToken->value, "\n"),
                    'endLine' => $line,
                    'startFilePos' => $filePos - \strlen($lastToken->value),
                    'endFilePos' => $filePos,
                ]));
            }
        }

        // Add an EOF sentinel token
        // TODO: Should the value be an empty string instead?
        $tokens[] = new Token(0, "\0", $line, \strlen($code));

        return $tokens;
    }

    private function handleInvalidCharacterRange(
        string $code, int $start, int $end, int $line, ErrorHandler $errorHandler
    ) {
        $tokens = [];
        for ($i = $start; $i < $end; $i++) {
            $chr = $code[$i];
            if ($chr === "\0") {
                // PHP cuts error message after null byte, so need special case
                $errorMsg = 'Unexpected null byte';
            } else {
                $errorMsg = sprintf(
                    'Unexpected character "%s" (ASCII %d)', $chr, ord($chr)
                );
            }

            $tokens[] = new Token(self::T_BAD_CHARACTER, $chr, $line, $i);
            $errorHandler->handleError(new Error($errorMsg, [
                'startLine' => $line,
                'endLine' => $line,
                'startFilePos' => $i,
                'endFilePos' => $i,
            ]));
        }
        return $tokens;
    }

    private function isUnterminatedComment(Token $token): bool {
        return ($token->id === \T_COMMENT || $token->id === \T_DOC_COMMENT)
            && substr($token->value, 0, 2) === '/*'
            && substr($token->value, -2) !== '*/';
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     * Whitespace and comment tokens are mapped to null, which
     * indicates that they should be dropped.
     *
     * @return array The token map
     */
    private function createTokenMap(): array {
        $tokenMap = [];

        // ASCII values map to themselves.
        for ($i = 0; $i < 256; ++$i) {
            $tokenMap[$i] = $i;
        }

        for (; $i < 1000; ++$i) {
            if (\T_DOUBLE_COLON === $i) {
                // T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
                $tokenMap[$i] = Tokens::T_PAAMAYIM_NEKUDOTAYIM;
            } elseif (\T_OPEN_TAG_WITH_ECHO === $i) {
                // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
                $tokenMap[$i] = Tokens::T_ECHO;
            } elseif (\T_CLOSE_TAG === $i) {
                // T_CLOSE_TAG is equivalent to ';'
                $tokenMap[$i] = ord(';');
            } elseif ('UNKNOWN' !== $name = token_name($i)) {
                if (defined($name = Tokens::class . '::' . $name)) {
                    // Other tokens can be mapped directly
                    $tokenMap[$i] = constant($name);
                }
            }
        }

        $dropTokens = [
            \T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT,
            // PHP only reports invalid characters during lexing and discards them for parsing
            self::T_BAD_CHARACTER
        ];
        foreach ($dropTokens as $dropToken) {
            $tokenMap[$dropToken] = null;
        }

        return $tokenMap;
    }
}
