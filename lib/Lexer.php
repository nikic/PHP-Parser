<?php

class Lexer
{
    protected $tokens;
    protected $pos;

    private static $tokenMap;
    private static $dropTokens = array(
        T_WHITESPACE => 1, T_COMMENT => 1, T_DOC_COMMENT => 1, T_OPEN_TAG => 1
    );

    public function __construct($code) {
        self::initTokenMap();

        $this->tokens = token_get_all($code);
        $this->pos    = -1;
    }

    public function yylex(&$yyLVal) {
        while (isset($this->tokens[++$this->pos])) {
            $token = $this->tokens[$this->pos];
            if (is_string($token)) {
                $yyLVal = $token;
                return ord($token);
            } elseif (!isset(self::$dropTokens[$token[0]])) {
                $yyLVal = $token[1];
                return self::$tokenMap[$token[0]];
            }
        }

        return 0;
    }

    /**
     * Returns the line the current token is in.
     *
     * @return int
     */
    public function getLine() {
        for ($i = $this->pos; $i > 0; --$i) {
            if (is_array($this->tokens[$this->pos])) {
                return $this->tokens[$this->pos][2];
            }
        }

        return -1;
    }

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