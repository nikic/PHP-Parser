<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Tokens;

require __DIR__ . '/compatibility_tokens.php';

class Lexer
{
    /** @var string Code being tokenized */
    protected $code;
    /** @var Token[] Array of tokens */
    protected $tokens;
    /** @var int Current position in the token array */
    protected $pos;
    protected $prevCloseTagHasNewline;

    protected $tokenMap;
    protected $dropTokens;

    private $attributeStartLineUsed;
    private $attributeEndLineUsed;
    private $attributeStartTokenPosUsed;
    private $attributeEndTokenPosUsed;
    private $attributeStartFilePosUsed;
    private $attributeEndFilePosUsed;
    private $attributeCommentsUsed;

    /**
     * Creates a Lexer.
     *
     * @param array $options Options array. Currently only the 'usedAttributes' option is supported,
     *                       which is an array of attributes to add to the AST nodes. Possible
     *                       attributes are: 'comments', 'startLine', 'endLine', 'startTokenPos',
     *                       'endTokenPos', 'startFilePos', 'endFilePos'. The option defaults to the
     *                       first three. For more info see getNextToken() docs.
     */
    public function __construct(array $options = []) {
        // map of tokens to drop while lexing (the map is only used for isset lookup,
        // that's why the value is simply set to 1; the value is never actually used.)
        $this->dropTokens = array_fill_keys(
            [\T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT, \T_BAD_CHARACTER], 1
        );

        $defaultAttributes = ['comments', 'startLine', 'endLine'];
        $usedAttributes = array_fill_keys($options['usedAttributes'] ?? $defaultAttributes, true);

        // Create individual boolean properties to make these checks faster.
        $this->attributeStartLineUsed = isset($usedAttributes['startLine']);
        $this->attributeEndLineUsed = isset($usedAttributes['endLine']);
        $this->attributeStartTokenPosUsed = isset($usedAttributes['startTokenPos']);
        $this->attributeEndTokenPosUsed = isset($usedAttributes['endTokenPos']);
        $this->attributeStartFilePosUsed = isset($usedAttributes['startFilePos']);
        $this->attributeEndFilePosUsed = isset($usedAttributes['endFilePos']);
        $this->attributeCommentsUsed = isset($usedAttributes['comments']);
    }

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
    public function startLexing(string $code, ErrorHandler $errorHandler = null) {
        if (null === $errorHandler) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        $this->code = $code; // keep the code around for __halt_compiler() handling
        $this->pos = -1;

        // If inline HTML occurs without preceding code, treat it as if it had a leading newline.
        // This ensures proper composability, because having a newline is the "safe" assumption.
        $this->prevCloseTagHasNewline = true;

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

    protected function postprocessTokens(ErrorHandler $errorHandler) {
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
     * Fetches the next token.
     *
     * The available attributes are determined by the 'usedAttributes' option, which can
     * be specified in the constructor. The following attributes are supported:
     *
     *  * 'comments'      => Array of PhpParser\Comment or PhpParser\Comment\Doc instances,
     *                       representing all comments that occurred between the previous
     *                       non-discarded token and the current one.
     *  * 'startLine'     => Line in which the node starts.
     *  * 'endLine'       => Line in which the node ends.
     *  * 'startTokenPos' => Offset into the token array of the first token in the node.
     *  * 'endTokenPos'   => Offset into the token array of the last token in the node.
     *  * 'startFilePos'  => Offset into the code string of the first character that is part of the node.
     *  * 'endFilePos'    => Offset into the code string of the last character that is part of the node.
     *
     * @param mixed $value           Variable to store token content in
     * @param mixed $startAttributes Variable to store start attributes in
     * @param mixed $endAttributes   Variable to store end attributes in
     *
     * @return int Token id
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null): int {
        $startAttributes = [];
        $endAttributes   = [];

        while (1) {
            $token = $this->tokens[++$this->pos];

            if ($this->attributeStartLineUsed) {
                $startAttributes['startLine'] = $token->line;
            }
            if ($this->attributeStartTokenPosUsed) {
                $startAttributes['startTokenPos'] = $this->pos;
            }
            if ($this->attributeStartFilePosUsed) {
                $startAttributes['startFilePos'] = $token->pos;
            }

            $id = $token->id;
            if (isset($this->dropTokens[$id])) {
                if (\T_COMMENT === $id || \T_DOC_COMMENT === $id) {
                    if ($this->attributeCommentsUsed) {
                        $comment = \T_DOC_COMMENT === $id
                            ? new Comment\Doc($token->text, $token->line, $token->pos, $this->pos,
                                $token->getEndLine(), $token->getEndPos() - 1, $this->pos)
                            : new Comment($token->text, $token->line, $token->pos, $this->pos,
                                $token->getEndLine(), $token->getEndPos() - 1, $this->pos);
                        $startAttributes['comments'][] = $comment;
                    }
                }
                continue;
            }

            $value = $token->text;
            if (\T_CLOSE_TAG === $token->id) {
                $this->prevCloseTagHasNewline = false !== strpos($value, "\n")
                    || false !== strpos($value, "\r");
            } elseif (\T_INLINE_HTML === $token->id) {
                $startAttributes['hasLeadingNewline'] = $this->prevCloseTagHasNewline;
            }

            // Fetch the end line/pos from the next token (if available) instead of recomputing it.
            $nextToken = $this->tokens[$this->pos + 1] ?? null;
            if ($this->attributeEndLineUsed) {
                $endAttributes['endLine'] = $nextToken ? $nextToken->line : $token->getEndLine();
            }
            if ($this->attributeEndTokenPosUsed) {
                $endAttributes['endTokenPos'] = $this->pos;
            }
            if ($this->attributeEndFilePosUsed) {
                $endAttributes['endFilePos'] = ($nextToken ? $nextToken->pos : $token->getEndPos()) - 1;
            }

            return $id;
        }
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

    /**
     * Handles __halt_compiler() by returning the text after it.
     *
     * @return string Remaining text
     */
    public function handleHaltCompiler(): string {
        // Prevent the lexer from returning any further tokens.
        $nextToken = $this->tokens[$this->pos + 1];
        $this->pos = \count($this->tokens) - 2;

        // Return text after __halt_compiler.
        return $nextToken->id === \T_INLINE_HTML ? $nextToken->text : '';
    }
}
