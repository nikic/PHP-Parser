<?php declare(strict_types=1);

namespace PhpParser;

require __DIR__ . '/compatibility_tokens.php';

class Lexer {
    /** @var list<Token> List of tokens */
    protected $tokens;

    /**
     * Initializes the lexer for lexing the provided source code.
     *
     * This function does not throw if lexing errors occur. Instead, errors may be retrieved using
     * the getErrors() method.
     *
     * @param string $code The source code to lex
     * @param ErrorHandler|null $errorHandler Error handler to use for lexing errors. Defaults to
     *                                        ErrorHandler\Throwing
     */
    public function startLexing(string $code, ?ErrorHandler $errorHandler = null): void {
        if (null === $errorHandler) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        $scream = ini_set('xdebug.scream', '0');

        $this->tokens = @Token::tokenize($code);
        $this->postprocessTokens($errorHandler);

        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }
    }

    private function handleInvalidCharacter(Token $token, ErrorHandler $errorHandler): void {
        $chr = $token->text;
        if ($chr === "\0") {
            // PHP cuts error message after null byte, so need special case
            $errorMsg = 'Unexpected null byte';
        } else {
            $errorMsg = sprintf(
                'Unexpected character "%s" (ASCII %d)', $chr, ord($chr)
            );
        }

        $errorHandler->handleError(new Error($errorMsg, [
            'startLine' => $token->line,
            'endLine' => $token->line,
            'startFilePos' => $token->pos,
            'endFilePos' => $token->pos,
        ]));
    }

    private function isUnterminatedComment(Token $token): bool {
        return $token->is([\T_COMMENT, \T_DOC_COMMENT])
            && substr($token->text, 0, 2) === '/*'
            && substr($token->text, -2) !== '*/';
    }

    protected function postprocessTokens(ErrorHandler $errorHandler): void {
        // This function reports errors (bad characters and unterminated comments) in the token
        // array, and performs certain canonicalizations:
        //  * Use PHP 8.1 T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG and
        //    T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG tokens used to disambiguate intersection types.
        //  * Add a sentinel token with ID 0.

        $numTokens = \count($this->tokens);
        if ($numTokens === 0) {
            // Empty input edge case: Just add the sentinel token.
            $this->tokens[] = new Token(0, "\0", 1, 0);
            return;
        }

        for ($i = 0; $i < $numTokens; $i++) {
            $token = $this->tokens[$i];
            if ($token->id === \T_BAD_CHARACTER) {
                $this->handleInvalidCharacter($token, $errorHandler);
            }

            if ($token->id === \ord('&')) {
                $next = $i + 1;
                while (isset($this->tokens[$next]) && $this->tokens[$next]->id === \T_WHITESPACE) {
                    $next++;
                }
                $followedByVarOrVarArg = isset($this->tokens[$next]) &&
                    $this->tokens[$next]->is([\T_VARIABLE, \T_ELLIPSIS]);
                $token->id = $followedByVarOrVarArg
                    ? \T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
                    : \T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
            }
        }

        // Check for unterminated comment
        $lastToken = $this->tokens[$numTokens - 1];
        if ($this->isUnterminatedComment($lastToken)) {
            $errorHandler->handleError(new Error('Unterminated comment', [
                'startLine' => $lastToken->line,
                'endLine' => $lastToken->getEndLine(),
                'startFilePos' => $lastToken->pos,
                'endFilePos' => $lastToken->getEndPos(),
            ]));
        }

        // Add sentinel token.
        $this->tokens[] = new Token(0, "\0", $lastToken->getEndLine(), $lastToken->getEndPos());
    }

    /**
     * Returns the token array for current code.
     *
     * The token array is in the same format as provided by the PhpToken::tokenize() method in
     * PHP 8.0. The tokens are instances of PhpParser\Token, to abstract over a polyfill
     * implementation in earlier PHP version.
     *
     * The token array is terminated by a sentinel token with token ID 0.
     * The token array does not discard any tokens (i.e. whitespace and comments are included).
     * The token position attributes are against this token array.
     *
     * @return Token[] Array of tokens
     */
    public function getTokens(): array {
        return $this->tokens;
    }
}
