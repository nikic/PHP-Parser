<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Tokens;

class Lexer
{
    /* Token ID used for illegal characters part of the token stream. These are dropped by token_get_all(),
     * but we restore them here to make sure that the tokens cover the full original text, and to prevent
     * file positions from going out of sync. */
    const T_BAD_CHARACTER = -1;

    /** @var string */
    protected $code;
    /** @var Token[] */
    protected $tokens;
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
        // map from internal tokens to PhpParser tokens
        $this->tokenMap = $this->createTokenMap();

        // map of tokens to drop while lexing (the map is only used for isset lookup,
        // that's why the value is simply set to 1; the value is never actually used.)
        $this->dropTokens = array_fill_keys(
            [\T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT, self::T_BAD_CHARACTER], 1
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
        $this->pos  = -1;
        $this->line =  1;
        $this->filePos = 0;

        // If inline HTML occurs without preceding code, treat it as if it had a leading newline.
        // This ensures proper composability, because having a newline is the "safe" assumption.
        $this->prevCloseTagHasNewline = true;

        $scream = ini_set('xdebug.scream', '0');

        $this->tokens = $this->createNormalizedTokens($code, $errorHandler);

        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }
    }

    private function handleInvalidCharacterRange($start, $end, $line, ErrorHandler $errorHandler) {
        $tokens = [];
        for ($i = $start; $i < $end; $i++) {
            $chr = $this->code[$i];
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

    /**
     * Check whether comment token is unterminated.
     *
     * @return bool
     */
    private function isUnterminatedComment(Token $token) : bool {
        return ($token->id === \T_COMMENT || $token->id === \T_DOC_COMMENT)
            && substr($token->value, 0, 2) === '/*'
            && substr($token->value, -2) !== '*/';
    }

    /**
     * Check whether an error *may* have occurred during tokenization.
     *
     * @return bool
     */
    private function errorMayHaveOccurred() : bool {
        if (defined('HHVM_VERSION')) {
            // In HHVM token_get_all() does not throw warnings, so we need to conservatively
            // assume that an error occurred
            return true;
        }

        return null !== error_get_last();
    }

    private function createNormalizedTokens(string $code, ErrorHandler $errorHandler) {
        error_clear_last();
        $rawTokens = @token_get_all($code);
        $checkForMissingTokens = $this->errorMayHaveOccurred();

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
                    $filePos, $nextFilePos, $line, $errorHandler);
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
                $filePos, \strlen($code), $line, $errorHandler);
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
    public function getNextToken(&$value = null, &$startAttributes = null) : int {
        $startAttributes = [];

        while (1) {
            $token = $this->tokens[++$this->pos];

            $phpId = $token->id;
            $value = $token->value;
            if (!isset($this->dropTokens[$phpId])) {
                $id = $this->tokenMap[$phpId];
                if (\T_CLOSE_TAG === $phpId) {
                    $this->prevCloseTagHasNewline = false !== strpos($value, "\n");
                } elseif (\T_INLINE_HTML === $phpId) {
                    $startAttributes['hasLeadingNewline'] = $this->prevCloseTagHasNewline;
                }

                return $id;
            }

            if (\T_COMMENT === $phpId || \T_DOC_COMMENT === $phpId) {
                if ($this->attributeCommentsUsed) {
                    $comment = \T_DOC_COMMENT === $phpId
                        ? new Comment\Doc($value, $token->line, $token->filePos, $this->pos)
                        : new Comment($value, $token->line, $token->filePos, $this->pos);
                    $startAttributes['comments'][] = $comment;
                }
            }
        }

        throw new \RuntimeException('Reached end of lexer loop');
    }

    /**
     * Returns the token array for the current code.
     *
     * @return Token[]
     */
    public function getTokens() : array {
        return $this->tokens;
    }

    public function getTokenMap(): array {
        return $this->tokenMap;
    }

    /**
     * Handles __halt_compiler() by returning the text after it.
     *
     * @return string Remaining text
     */
    public function handleHaltCompiler() : string {
        // Text after T_HALT_COMPILER, still including ();
        $tokenAfter = $this->tokens[$this->pos + 1];
        $textAfter = substr($this->code, $tokenAfter->filePos);

        // ensure that it is followed by ();
        // this simplifies the situation, by not allowing any comments
        // in between of the tokens.
        if (!preg_match('~^\s*\(\s*\)\s*(?:;|\?>\r?\n?)~', $textAfter, $matches)) {
            throw new Error('__HALT_COMPILER must be followed by "();"');
        }

        // Point to one before EOF token, so it will be returned on the getNextToken() call
        $this->pos = count($this->tokens) - 2;

        // return with (); removed
        return substr($textAfter, strlen($matches[0]));
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     * Whitespace and comment tokens are mapped to null, which indicates
     * that they should be dropped.
     *
     * @return array The token map
     */
    protected function createTokenMap() : array {
        $tokenMap = [];

        // ASCII values map to themselves.
        for ($i = 0; $i < 256; ++$i) {
            $tokenMap[$i] = $i;
        }

        for (; $i < 1000; ++$i) {
            if (\T_DOUBLE_COLON === $i) {
                // T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
                $tokenMap[$i] = Tokens::T_PAAMAYIM_NEKUDOTAYIM;
            } elseif(\T_OPEN_TAG_WITH_ECHO === $i) {
                // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
                $tokenMap[$i] = Tokens::T_ECHO;
            } elseif(\T_CLOSE_TAG === $i) {
                // T_CLOSE_TAG is equivalent to ';'
                $tokenMap[$i] = ord(';');
            } elseif ('UNKNOWN' !== $name = token_name($i)) {
                if (defined($name = Tokens::class . '::' . $name)) {
                    // Other tokens can be mapped directly
                    $tokenMap[$i] = constant($name);
                }
            }
        }

        $dropTokens = [\T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT, self::T_BAD_CHARACTER];
        foreach ($dropTokens as $dropToken) {
            $tokenMap[$dropToken] = null;
        }

        return $tokenMap;
    }
}
