<?php

namespace PhpParser\Lexer;

use PhpParser\Parser;

/**
 * ATTENTION: This code is WRITE-ONLY. Do not try to read it.
 */
class Emulative extends \PhpParser\Lexer
{
    protected $newKeywords;
    protected $inObjectAccess;

    public function __construct() {
        parent::__construct();

        $newKeywordsPerVersion = array(
            '5.5.0-dev' => array(
                'finally'       => Parser::T_FINALLY,
                'yield'         => Parser::T_YIELD,
            ),
            '5.4.0-dev' => array(
                'callable'      => Parser::T_CALLABLE,
                'insteadof'     => Parser::T_INSTEADOF,
                'trait'         => Parser::T_TRAIT,
                '__trait__'     => Parser::T_TRAIT_C,
            ),
        );

        $this->newKeywords = array();
        foreach ($newKeywordsPerVersion as $version => $newKeywords) {
            if (version_compare(PHP_VERSION, $version, '>=')) {
                break;
            }

            $this->newKeywords += $newKeywords;
        }
    }

    public function startLexing($code) {
        $this->inObjectAccess = false;

        // on PHP 5.4 don't do anything
        if (version_compare(PHP_VERSION, '5.4.0RC1', '>=')) {
            parent::startLexing($code);
        } else {
            $code = $this->preprocessCode($code);
            parent::startLexing($code);
            $this->postprocessTokens();
        }
    }

    /*
     * Replaces new features in the code by ~__EMU__{NAME}__{DATA}__~ sequences.
     * ~LABEL~ is never valid PHP code, that's why we can (to some degree) safely
     * use it here.
     * Later when preprocessing the tokens these sequences will either be replaced
     * by real tokens or replaced with their original content (e.g. if they occured
     * inside a string, i.e. a place where they don't have a special meaning).
     */
    protected function preprocessCode($code) {
        // binary notation (0b010101101001...)
        return preg_replace('(\b0b[01]+\b)', '~__EMU__BINARY__$0__~', $code);
    }

    /*
     * Replaces the ~__EMU__...~ sequences with real tokens or their original
     * value.
     */
    protected function postprocessTokens() {
        // we need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($this->tokens); $i < $c; ++$i) {
            // first check that the following tokens are of form ~LABEL~,
            // then match the __EMU__... sequence.
            if ('~' === $this->tokens[$i]
                && isset($this->tokens[$i + 2])
                && '~' === $this->tokens[$i + 2]
                && T_STRING === $this->tokens[$i + 1][0]
                && preg_match('(^__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?$)', $this->tokens[$i + 1][1], $matches)
            ) {
                if ('BINARY' === $matches[1]) {
                    // the binary number can either be an integer or a double, so return a LNUMBER
                    // or DNUMBER respectively
                    $replace = array(
                        array(is_int(bindec($matches[2])) ? T_LNUMBER : T_DNUMBER, $matches[2], $this->tokens[$i + 1][2])
                    );
                } else {
                    // just ignore all other __EMU__ sequences
                    continue;
                }

                array_splice($this->tokens, $i, 3, $replace);
                $c -= 3 - count($replace);
            // for multichar tokens (e.g. strings) replace any ~__EMU__...~ sequences
            // in their content with the original character sequence
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

    /*
     * This method is a callback for restoring EMU sequences in
     * multichar tokens (like strings) to their original value.
     */
    public function restoreContentCallback(array $matches) {
        if ('BINARY' === $matches[1]) {
            return $matches[2];
        } else {
            return $matches[0];
        }
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $token = parent::getNextToken($value, $startAttributes, $endAttributes);

        // replace new keywords by their respective tokens. This is not done
        // if we currently are in an object access (e.g. in $obj->namespace
        // "namespace" stays a T_STRING tokens and isn't converted to T_NAMESPACE)
        if (Parser::T_STRING === $token && !$this->inObjectAccess) {
            if (isset($this->newKeywords[strtolower($value)])) {
                return $this->newKeywords[strtolower($value)];
            }
        // keep track of whether we currently are in an object access (after ->)
        } elseif (Parser::T_OBJECT_OPERATOR === $token) {
            $this->inObjectAccess = true;
        } else {
            $this->inObjectAccess = false;
        }

        return $token;
    }
}