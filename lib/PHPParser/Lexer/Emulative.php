<?php

/**
 * ATTENTION: This code is WRITE-ONLY. Do not try to read it.
 */
class PHPParser_Lexer_Emulative extends PHPParser_Lexer
{
    protected static $keywords = array(
        // PHP 5.4
        'callable'      => PHPParser_Parser::T_CALLABLE,
        'insteadof'     => PHPParser_Parser::T_INSTEADOF,
        'trait'         => PHPParser_Parser::T_TRAIT,
        '__trait__'     => PHPParser_Parser::T_TRAIT_C,
        // PHP 5.3
        '__dir__'       => PHPParser_Parser::T_DIR,
        'goto'          => PHPParser_Parser::T_GOTO,
        'namespace'     => PHPParser_Parser::T_NAMESPACE,
        '__namespace__' => PHPParser_Parser::T_NS_C,
    );

    protected $inObjectAccess;

    public function __construct($code) {
        $this->inObjectAccess = false;

        if (version_compare(PHP_VERSION, '5.4.0RC1', '<')) {
            // binary notation
            $code = preg_replace('(\b0b[01]+\b)', '~__EMU__BINARY__$0__~', $code);
        }

        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            // namespace separator
            $code = preg_replace('(\\\\(?!["\'`$\\\\]))', '~__EMU__NS__~', $code);

            // nowdoc
            $code = preg_replace_callback(
                '((*BSR_ANYCRLF)        # set \R to (?>\r\n|\r|\n)
                  (b?<<<[\t ]*\'([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\'\R) # opening token
                  ((?:(?!\2;?\R).*\R)*) # content
                  (\2)                  # closing token
                  (?=;?\R)              # must be followed by newline (with optional semicolon)
                 )x',
                array($this, 'encodeNowdocCallback'),
                $code
            );
        }

        parent::__construct($code);

        for ($i = 0, $c = count($this->tokens); $i < $c; ++$i) {
            if ('~' === $this->tokens[$i]
                && isset($this->tokens[$i + 2])
                && '~' === $this->tokens[$i + 2]
                && T_STRING === $this->tokens[$i + 1][0]
                && preg_match('(^__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?$)', $this->tokens[$i + 1][1], $matches)
            ) {
                if ('BINARY' === $matches[1]) {
                    $replace = array(
                        array(is_int(bindec($matches[2])) ? T_LNUMBER : T_DNUMBER, $matches[2], $this->tokens[$i + 1][2])
                    );
                } elseif ('NS' === $matches[1]) {
                    $replace = array('\\');
                } elseif ('NOWDOC' === $matches[1]) {
                    list($start, $content, $end) = explode('x', $matches[2]);
                    list($start, $content, $end) = array(pack('H*', $start), pack('H*', $content), pack('H*', $end));

                    $replace = array();
                    $replace[] = array(T_START_HEREDOC, $start, $this->tokens[$i + 1][2]);
                    if ('' !== $content) {
                        $replace[] = array(T_ENCAPSED_AND_WHITESPACE, $content, -1);
                    }
                    $replace[] = array(T_END_HEREDOC, $end, -1);
                } else {
                    continue;
                }

                array_splice($this->tokens, $i, 3, $replace);
                $c -= 3 - count($replace);
            } elseif (is_array($this->tokens[$i])
                      && 0 !== strpos($this->tokens[$i][1], '__EMU__')
            ) {
                $this->tokens[$i][1] = preg_replace_callback(
                    '(~__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?~)',
                    array($this, 'restoreContentCallback'),
                    $this->tokens[$i][1]
                );
            }
        }
    }

    public function encodeNowdocCallback(array $matches) {
        return '~__EMU__NOWDOC__'
             . bin2hex($matches[1]) . 'x' . bin2hex($matches[3]) . 'x' . bin2hex($matches[4])
             . '__~';
    }

    public function restoreContentCallback(array $matches) {
        if ('BINARY' === $matches[1]) {
            return $matches[2];
        } elseif ('NS' === $matches[1]) {
            return '\\';
        } elseif ('NOWDOC' === $matches[1]) {
            list($start, $content, $end) = explode('x', $matches[2]);
            return pack('H*', $start) . pack('H*', $content) . pack('H*', $end);
        } else {
            return $matches[0];
        }
    }

    public function lex(&$value = null, &$line = null, &$docComment = null) {
        $token = parent::lex($value, $line, $docComment);

        if (PHPParser_Parser::T_STRING === $token && !$this->inObjectAccess) {
            if (isset(self::$keywords[strtolower($value)])) {
                return self::$keywords[strtolower($value)];
            }
        } elseif (92 === $token) { // ord('\\')
            return PHPParser_Parser::T_NS_SEPARATOR;
        } elseif (PHPParser_Parser::T_OBJECT_OPERATOR === $token) {
            $this->inObjectAccess = true;
        } else {
            $this->inObjectAccess = false;
        }

        return $token;
    }
}