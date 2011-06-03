<?php

class Lexer
{
    protected $code;
    protected $tokens;
    protected $pos;

    private static $tokenMap;
    private static $dropTokens = array(
        T_WHITESPACE => 1, T_COMMENT => 1, T_DOC_COMMENT => 1, T_OPEN_TAG => 1
    );

    /**
     * Creates a Lexer.
     *
     * @param string $code
     */
    public function __construct($code) {
        self::initTokenMap();

        // Reset the error message in error_get_last()
        // Still hoping for a better solution to be found.
        @$errorGetLastResetUndefinedVariable;

        $this->code   = $code;
        $this->tokens = @token_get_all($code);
        $this->pos    = -1;

        $error = error_get_last();

        if (preg_match(
                '~^(Unterminated comment) starting line ([0-9]+)$~',
                $error['message'],
                $matches
            )
        ) {
            throw new ParseErrorException($matches[1], $matches[2]);
        }

        if (preg_match(
                '~^(Unexpected character in input:\s+\'(.)\' \(ASCII=[0-9]+\))~s',
                $error['message'],
                $matches
            )
        ) {
            throw new ParseErrorException($matches[1]);
        }
    }

    /**
     * Returns the next token id.
     *
     * @param mixed $lVal Variable to store token content in
     *
     * @return int Token id
     */
    public function lex(&$lVal) {
        while (isset($this->tokens[++$this->pos])) {
            $token = $this->tokens[$this->pos];

            if (is_string($token)) {
                $lVal = $token;
                return ord($token);
            } elseif (!isset(self::$dropTokens[$token[0]])) {
                $lVal = $token[1];
                return self::$tokenMap[$token[0]];
            }
        }

        return 0;
    }

    /**
     * Returns the line the current token is in.
     *
     * @return int Line current token is in
     */
    public function getLine() {
        for ($i = $this->pos - 1; $i--;) {
            if (is_array($this->tokens[$i])) {
                return $this->tokens[$i][2];
            }
        }

        return -1;
    }

    /**
     * Handles __halt_compiler() by returning the text after it.
     *
     * @return string Remaining text
     */
    public function handleHaltCompiler() {
        // get the length of the text before the T_HALT_COMPILER token
        $textBefore = '';
        for ($i = 0; $i <= $this->pos; ++$i) {
            if (is_string($this->tokens[$i])) {
                $textBefore .= $this->tokens[$i];
            } else {
                $textBefore .= $this->tokens[$i][1];
            }
        }

        // text after T_HALT_COMPILER, still including ();
        $textAfter = substr($this->code, strlen($textBefore));

        // ensure that it is followed by ();
        // this simplifies the situation, by not allowing any comments
        // in between of the tokens.
        if (!preg_match('~\s*\(\s*\)\s*;~', $textAfter, $matches)) {
            throw new ParseErrorException('__halt_compiler must be followed by "();"');
        }

        // prevent the lexer from returning any further tokens
        $this->pos = count($this->tokens);

        // return with (); removed
        return substr($textAfter, strlen($matches[0]));
    }

    /**
     * Initializes the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     */
    private static function initTokenMap() {
        if (!self::$tokenMap) {
            self::$tokenMap = array();

            // 256 is the minimum possible token number, as everything below
            // it is an ASCII value
            for ($i = 256; $i < 1000; ++$i) {
                // T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
                if (T_DOUBLE_COLON === $i) {
                    self::$tokenMap[$i] = Parser::T_PAAMAYIM_NEKUDOTAYIM;
                // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
                } elseif(T_OPEN_TAG_WITH_ECHO === $i) {
                    self::$tokenMap[$i] = Parser::T_ECHO;
                // T_CLOSE_TAG is equivalent to ';'
                } elseif(T_CLOSE_TAG === $i) {
                    self::$tokenMap[$i] = ord(';');
                // and the others can be mapped directly
                } elseif ('UNKNOWN' !== ($name = token_name($i))) {
                    self::$tokenMap[$i] = constant('Parser::' . $name);
                }
            }
        }
    }
}