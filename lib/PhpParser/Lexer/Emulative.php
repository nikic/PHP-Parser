<?php

namespace PhpParser\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\Parser\Tokens;

class Emulative extends \PhpParser\Lexer
{
    protected $newKeywords;
    protected $inObjectAccess;

    const T_ELLIPSIS   = 1001;
    const T_POW        = 1002;
    const T_POW_EQUAL  = 1003;
    const T_COALESCE   = 1004;
    const T_SPACESHIP  = 1005;
    const T_YIELD_FROM = 1006;

    const PHP_7_0 = '7.0.0dev';
    const PHP_5_6 = '5.6.0rc1';

    public function __construct(array $options = array()) {
        parent::__construct($options);

        $newKeywordsPerVersion = array(
            // No new keywords since PHP 5.5
        );

        $this->newKeywords = array();
        foreach ($newKeywordsPerVersion as $version => $newKeywords) {
            if (version_compare(PHP_VERSION, $version, '>=')) {
                break;
            }

            $this->newKeywords += $newKeywords;
        }

        if (version_compare(PHP_VERSION, self::PHP_7_0, '>=')) {
            return;
        }
        $this->tokenMap[self::T_COALESCE]   = Tokens::T_COALESCE;
        $this->tokenMap[self::T_SPACESHIP]  = Tokens::T_SPACESHIP;
        $this->tokenMap[self::T_YIELD_FROM] = Tokens::T_YIELD_FROM;

        if (version_compare(PHP_VERSION, self::PHP_5_6, '>=')) {
            return;
        }
        $this->tokenMap[self::T_ELLIPSIS]  = Tokens::T_ELLIPSIS;
        $this->tokenMap[self::T_POW]       = Tokens::T_POW;
        $this->tokenMap[self::T_POW_EQUAL] = Tokens::T_POW_EQUAL;
    }

    public function startLexing($code, ErrorHandler $errorHandler = null) {
        $this->inObjectAccess = false;

        parent::startLexing($code, $errorHandler);
        if ($this->requiresEmulation($code)) {
            $this->emulateTokens();
        }
    }

    /*
     * Checks if the code is potentially using features that require emulation.
     */
    protected function requiresEmulation($code) {
        if (version_compare(PHP_VERSION, self::PHP_7_0, '>=')) {
            return false;
        }

        if (preg_match('(\?\?|<=>|yield[ \n\r\t]+from)', $code)) {
            return true;
        }

        if (version_compare(PHP_VERSION, self::PHP_5_6, '>=')) {
            return false;
        }

        return preg_match('(\.\.\.|(?<!/)\*\*(?!/))', $code);
    }

    /*
     * Emulates tokens for newer PHP versions.
     */
    protected function emulateTokens() {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        $line = 1;
        for ($i = 0, $c = count($this->tokens); $i < $c; ++$i) {
            $replace = null;
            if (isset($this->tokens[$i + 1])) {
                if ($this->tokens[$i] === '?' && $this->tokens[$i + 1] === '?') {
                    array_splice($this->tokens, $i, 2, array(
                        array(self::T_COALESCE, '??', $line)
                    ));
                    $c--;
                    continue;
                }
                if ($this->tokens[$i][0] === T_IS_SMALLER_OR_EQUAL
                    && $this->tokens[$i + 1] === '>'
                ) {
                    array_splice($this->tokens, $i, 2, array(
                        array(self::T_SPACESHIP, '<=>', $line)
                    ));
                    $c--;
                    continue;
                }
                if ($this->tokens[$i] === '*' && $this->tokens[$i + 1] === '*') {
                    array_splice($this->tokens, $i, 2, array(
                        array(self::T_POW, '**', $line)
                    ));
                    $c--;
                    continue;
                }
                if ($this->tokens[$i] === '*' && $this->tokens[$i + 1][0] === T_MUL_EQUAL) {
                    array_splice($this->tokens, $i, 2, array(
                        array(self::T_POW_EQUAL, '**=', $line)
                    ));
                    $c--;
                    continue;
                }
            }

            if (isset($this->tokens[$i + 2])) {
                if ($this->tokens[$i][0] === T_YIELD && $this->tokens[$i + 1][0] === T_WHITESPACE
                    && $this->tokens[$i + 2][0] === T_STRING
                    && !strcasecmp($this->tokens[$i + 2][1], 'from')
                ) {
                    array_splice($this->tokens, $i, 3, array(
                        array(
                            self::T_YIELD_FROM,
                            $this->tokens[$i][1] . $this->tokens[$i + 1][1] . $this->tokens[$i + 2][1],
                            $line
                        )
                    ));
                    $c -= 2;
                    $line += substr_count($this->tokens[$i][1], "\n");
                    continue;
                }
                if ($this->tokens[$i] === '.' && $this->tokens[$i + 1] === '.'
                    && $this->tokens[$i + 2] === '.'
                ) {
                    array_splice($this->tokens, $i, 3, array(
                        array(self::T_ELLIPSIS, '...', $line)
                    ));
                    $c -= 2;
                    continue;
                }
            }

            if (\is_array($this->tokens[$i])) {
                $line += substr_count($this->tokens[$i][1], "\n");
            }
        }
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $token = parent::getNextToken($value, $startAttributes, $endAttributes);

        // replace new keywords by their respective tokens. This is not done
        // if we currently are in an object access (e.g. in $obj->namespace
        // "namespace" stays a T_STRING tokens and isn't converted to T_NAMESPACE)
        if (Tokens::T_STRING === $token && !$this->inObjectAccess) {
            if (isset($this->newKeywords[strtolower($value)])) {
                return $this->newKeywords[strtolower($value)];
            }
        } else {
            // keep track of whether we currently are in an object access (after ->)
            $this->inObjectAccess = Tokens::T_OBJECT_OPERATOR === $token;
        }

        return $token;
    }
}
