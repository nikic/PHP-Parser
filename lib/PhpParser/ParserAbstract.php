<?php

namespace PhpParser;

/*
 * This parser is based on a skeleton written by Moriyoshi Koizumi, which in
 * turn is based on work by Masato Bito.
 */
abstract class ParserAbstract
{
    /* The following dummy data must be provided by the extending class: */

    const TOKEN_INVALID = 0;
    const TOKEN_MAP_SIZE = 0;

    const YYLAST       = 0;
    const YY2TBLSTATE  = 0;
    const YYGLAST      = 0;
    const YYNLSTATES   = 0;
    const YYUNEXPECTED = 0;
    const YYDEFAULT    = 0;

    /* @var array Map of token ids to their respective names */
    protected static $terminals;
    /* @var array Map which translates lexer tokens to internal tokens */
    protected static $translate;

    protected static $yyaction;
    protected static $yycheck;
    protected static $yybase;
    protected static $yydefault;
    protected static $yygoto;
    protected static $yygcheck;
    protected static $yygbase;
    protected static $yygdefault;
    protected static $yylhs;
    protected static $yylen;

    /* This is optional data only necessary when debugging */
    protected static $yyproduction;

    /* End of dummy data */

    const TOKEN_NONE = -1;

    protected $yyval;
    protected $yyastk;
    protected $stackPos;
    protected $lexer;

    /**
     * Creates a parser instance.
     *
     * @param Lexer $lexer A lexer
     */
    public function __construct(Lexer $lexer) {
        $this->lexer = $lexer;
    }

    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     *
     * @return Node[] Array of statements
     */
    public function parse($code) {
        $this->lexer->startLexing($code);

        // We start off with no lookahead-token
        $tokenId = self::TOKEN_NONE;

        // The attributes for a node are taken from the first and last token of the node.
        // From the first token only the startAttributes are taken and from the last only
        // the endAttributes. Both are merged using the array union operator (+).
        $startAttributes = array('startLine' => 1);
        $endAttributes   = array();

        // In order to figure out the attributes for the starting token, we have to keep
        // them in a stack
        $attributeStack = array($startAttributes);

        // Start off in the initial state and keep a stack of previous states
        $state = 0;
        $stateStack = array($state);

        // AST stack (?)
        $this->yyastk = array();

        // Current position in the stack(s)
        $this->stackPos = 0;

        for (;;) {
            //$this->traceNewState($state, $tokenId);

            if (static::$yybase[$state] == 0) {
                $yyn = static::$yydefault[$state];
            } else {
                if ($tokenId === self::TOKEN_NONE) {
                    // Fetch the next token id from the lexer and fetch additional info by-ref.
                    // The end attributes are fetched into a temporary variable and only set once the token is really
                    // shifted (not during read). Otherwise you would sometimes get off-by-one errors, when a rule is
                    // reduced after a token was read but not yet shifted.
                    $origTokenId = $this->lexer->getNextToken($tokenValue, $startAttributes, $nextEndAttributes);

                    // map the lexer token id to the internally used token id's
                    $tokenId = $origTokenId >= 0 && $origTokenId < static::TOKEN_MAP_SIZE
                        ? static::$translate[$origTokenId]
                        : static::TOKEN_INVALID;

                    if ($tokenId === static::TOKEN_INVALID) {
                        throw new \RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $origTokenId, $tokenValue
                        ));
                    }

                    $attributeStack[$this->stackPos] = $startAttributes;

                    //$this->traceRead($tokenId);
                }

                if ((($yyn = static::$yybase[$state] + $tokenId) >= 0
                     && $yyn < static::YYLAST && static::$yycheck[$yyn] == $tokenId
                     || ($state < static::YY2TBLSTATE
                        && ($yyn = static::$yybase[$state + static::YYNLSTATES] + $tokenId) >= 0
                        && $yyn < static::YYLAST
                        && static::$yycheck[$yyn] == $tokenId))
                    && ($yyn = static::$yyaction[$yyn]) != static::YYDEFAULT) {
                    /*
                     * >= YYNLSTATE: shift and reduce
                     * > 0: shift
                     * = 0: accept
                     * < 0: reduce
                     * = -YYUNEXPECTED: error
                     */
                    if ($yyn > 0) {
                        /* shift */
                        //$this->traceShift($tokenId);

                        ++$this->stackPos;
                        $stateStack[$this->stackPos]     = $state = $yyn;
                        $this->yyastk[$this->stackPos]   = $tokenValue;
                        $attributeStack[$this->stackPos] = $startAttributes;
                        $endAttributes = $nextEndAttributes;
                        $tokenId = self::TOKEN_NONE;

                        if ($yyn < static::YYNLSTATES)
                            continue;

                        /* $yyn >= YYNLSTATES means shift-and-reduce */
                        $yyn -= static::YYNLSTATES;
                    } else {
                        $yyn = -$yyn;
                    }
                } else {
                    $yyn = static::$yydefault[$state];
                }
            }

            for (;;) {
                /* reduce/error */
                if ($yyn == 0) {
                    /* accept */
                    // $this->traceAccept();
                    return $this->yyval;
                } elseif ($yyn != static::YYUNEXPECTED) {
                    /* reduce */
                    // $this->traceReduce($yyn);

                    try {
                        $this->{'yyn' . $yyn}(
                            $attributeStack[$this->stackPos - static::$yylen[$yyn]]
                            + $endAttributes
                        );
                    } catch (Error $e) {
                        if (-1 === $e->getRawLine()) {
                            $e->setRawLine($startAttributes['startLine']);
                        }

                        throw $e;
                    }

                    /* Goto - shift nonterminal */
                    $this->stackPos -= static::$yylen[$yyn];
                    $yyn = static::$yylhs[$yyn];
                    if (($yyp = static::$yygbase[$yyn] + $stateStack[$this->stackPos]) >= 0
                         && $yyp < static::YYGLAST
                         && static::$yygcheck[$yyp] == $yyn) {
                        $state = static::$yygoto[$yyp];
                    } else {
                        $state = static::$yygdefault[$yyn];
                    }

                    ++$this->stackPos;

                    $stateStack[$this->stackPos]     = $state;
                    $this->yyastk[$this->stackPos]   = $this->yyval;
                    $attributeStack[$this->stackPos] = $startAttributes;
                } else {
                    /* error */
                    $expected = array();

                    $base = static::$yybase[$state];
                    for ($i = 0; $i < static::TOKEN_MAP_SIZE; ++$i) {
                        $n = $base + $i;
                        if ($n >= 0 && $n < static::YYLAST && static::$yycheck[$n] == $i
                         || $state < static::YY2TBLSTATE
                            && ($n = static::$yybase[$state + static::YYNLSTATES] + $i) >= 0
                            && $n < static::YYLAST && static::$yycheck[$n] == $i
                        ) {
                            if (static::$yyaction[$n] != static::YYUNEXPECTED) {
                                if (count($expected) == 4) {
                                    /* Too many expected tokens */
                                    $expected = array();
                                    break;
                                }

                                $expected[] = static::$terminals[$i];
                            }
                        }
                    }

                    $expectedString = '';
                    if ($expected) {
                        $expectedString = ', expecting ' . implode(' or ', $expected);
                    }

                    throw new Error(
                        'Syntax error, unexpected ' . static::$terminals[$tokenId] . $expectedString,
                        $startAttributes['startLine']
                    );
                }

                if ($state < static::YYNLSTATES)
                    break;
                /* >= YYNLSTATES means shift-and-reduce */
                $yyn = $state - static::YYNLSTATES;
            }
        }
    }

    protected function traceNewState($state, $tokenId) {
        echo '% State ' . $state
            . ', Lookahead ' . ($tokenId == self::TOKEN_NONE ? '--none--' : static::$terminals[$tokenId]) . "\n";
    }

    protected function traceRead($tokenId) {
        echo '% Reading ' . static::$terminals[$tokenId] . "\n";
    }

    protected function traceShift($tokenId) {
        echo '% Shift ' . static::$terminals[$tokenId] . "\n";
    }

    protected function traceAccept() {
        echo "% Accepted.\n";
    }

    protected function traceReduce($n) {
        echo '% Reduce by (' . $n . ') ' . static::$yyproduction[$n] . "\n";
    }
}
