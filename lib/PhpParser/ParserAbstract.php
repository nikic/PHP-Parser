<?php

namespace PhpParser;

/*
 * This parser is based on a skeleton written by Moriyoshi Koizumi, which in
 * turn is based on work by Masato Bito.
 */
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\UseUse;

abstract class ParserAbstract implements Parser
{
    const SYMBOL_NONE = -1;

    /*
     * The following members will be filled with generated parsing data:
     */

    /** @var int Size of $tokenToSymbol map */
    protected $tokenToSymbolMapSize;
    /** @var int Size of $action table */
    protected $actionTableSize;
    /** @var int Size of $goto table */
    protected $gotoTableSize;

    /** @var int Symbol number signifying an invalid token */
    protected $invalidSymbol;
    /** @var int Symbol number of error recovery token */
    protected $errorSymbol;
    /** @var int Action number signifying default action */
    protected $defaultAction;
    /** @var int Rule number signifying that an unexpected token was encountered */
    protected $unexpectedTokenRule;

    protected $YY2TBLSTATE;
    protected $YYNLSTATES;

    /** @var int[] Map of lexer tokens to internal symbols */
    protected $tokenToSymbol;
    /** @var string[] Map of symbols to their names */
    protected $symbolToName;
    /** @var array Names of the production rules (only necessary for debugging) */
    protected $productions;

    /** @var int[] Map of states to a displacement into the $action table. The corresponding action for this
     *             state/symbol pair is $action[$actionBase[$state] + $symbol]. If $actionBase[$state] is 0, the
                   action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionBase;
    /** @var int[] Table of actions. Indexed according to $actionBase comment. */
    protected $action;
    /** @var int[] Table indexed analogously to $action. If $actionCheck[$actionBase[$state] + $symbol] != $symbol
     *             then the action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionCheck;
    /** @var int[] Map of states to their default action */
    protected $actionDefault;
    /** @var callable[] Semantic action callbacks */
    protected $reduceCallbacks;

    /** @var int[] Map of non-terminals to a displacement into the $goto table. The corresponding goto state for this
     *             non-terminal/state pair is $goto[$gotoBase[$nonTerminal] + $state] (unless defaulted) */
    protected $gotoBase;
    /** @var int[] Table of states to goto after reduction. Indexed according to $gotoBase comment. */
    protected $goto;
    /** @var int[] Table indexed analogously to $goto. If $gotoCheck[$gotoBase[$nonTerminal] + $state] != $nonTerminal
     *             then the goto state is defaulted, i.e. $gotoDefault[$nonTerminal] should be used. */
    protected $gotoCheck;
    /** @var int[] Map of non-terminals to the default state to goto after their reduction */
    protected $gotoDefault;

    /** @var int[] Map of rules to the non-terminal on their left-hand side, i.e. the non-terminal to use for
     *             determining the state to goto after reduction. */
    protected $ruleToNonTerminal;
    /** @var int[] Map of rules to the length of their right-hand side, which is the number of elements that have to
     *             be popped from the stack(s) on reduction. */
    protected $ruleToLength;

    /*
     * The following members are part of the parser state:
     */

    /** @var Lexer Lexer that is used when parsing */
    protected $lexer;
    /** @var mixed Temporary value containing the result of last semantic action (reduction) */
    protected $semValue;
    /** @var array Semantic value stack (contains values of tokens and semantic action results) */
    protected $semStack;
    /** @var array[] Start attribute stack */
    protected $startAttributeStack;
    /** @var array[] End attribute stack */
    protected $endAttributeStack;
    /** @var array End attributes of last *shifted* token */
    protected $endAttributes;
    /** @var array Start attributes of last *read* token */
    protected $lookaheadStartAttributes;

    /** @var ErrorHandler Error handler */
    protected $errorHandler;
    /** @var Error[] Errors collected during last parse */
    protected $errors;
    /** @var int Error state, used to avoid error floods */
    protected $errorState;

    /**
     * Initialize $reduceCallbacks map.
     */
    abstract protected function initReduceCallbacks();

    /**
     * Creates a parser instance.
     *
     * Options: Currently none.
     *
     * @param Lexer $lexer A lexer
     * @param array $options Options array.
     */
    public function __construct(Lexer $lexer, array $options = []) {
        $this->lexer = $lexer;
        $this->errors = [];

        if (isset($options['throwOnError'])) {
            throw new \LogicException(
                '"throwOnError" is no longer supported, use "errorHandler" instead');
        }

        $this->initReduceCallbacks();
    }

    /**
     * Parses PHP code into a node tree.
     *
     * If a non-throwing error handler is used, the parser will continue parsing after an error
     * occurred and attempt to build a partial AST.
     *
     * @param string $code The source code to parse
     * @param ErrorHandler|null $errorHandler Error handler to use for lexer/parser errors, defaults
     *                                        to ErrorHandler\Throwing.
     *
     * @return Node\Stmt[]|null Array of statements (or null non-throwing error handler is used and
     *                          the parser was unable to recover from an error).
     */
    public function parse(string $code, ErrorHandler $errorHandler = null) {
        $this->errorHandler = $errorHandler ?: new ErrorHandler\Throwing;

        // Initialize the lexer
        $this->lexer->startLexing($code, $this->errorHandler);

        // We start off with no lookahead-token
        $symbol = self::SYMBOL_NONE;

        // The attributes for a node are taken from the first and last token of the node.
        // From the first token only the startAttributes are taken and from the last only
        // the endAttributes. Both are merged using the array union operator (+).
        $startAttributes = [];
        $endAttributes = [];
        $this->endAttributes = $endAttributes;

        // Keep stack of start and end attributes
        $this->startAttributeStack = [];
        $this->endAttributeStack = [$endAttributes];

        // Start off in the initial state and keep a stack of previous states
        $state = 0;
        $stateStack = [$state];

        // Semantic value stack (contains values of tokens and semantic action results)
        $this->semStack = [];

        // Current position in the stack(s)
        $stackPos = 0;

        $this->errorState = 0;

        for (;;) {
            //$this->traceNewState($state, $symbol);

            if ($this->actionBase[$state] === 0) {
                $rule = $this->actionDefault[$state];
            } else {
                if ($symbol === self::SYMBOL_NONE) {
                    // Fetch the next token id from the lexer and fetch additional info by-ref.
                    // The end attributes are fetched into a temporary variable and only set once the token is really
                    // shifted (not during read). Otherwise you would sometimes get off-by-one errors, when a rule is
                    // reduced after a token was read but not yet shifted.
                    $tokenId = $this->lexer->getNextToken($tokenValue, $startAttributes, $endAttributes);

                    // map the lexer token id to the internally used symbols
                    $symbol = $tokenId >= 0 && $tokenId < $this->tokenToSymbolMapSize
                        ? $this->tokenToSymbol[$tokenId]
                        : $this->invalidSymbol;

                    if ($symbol === $this->invalidSymbol) {
                        throw new \RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $tokenId, $tokenValue
                        ));
                    }

                    // This is necessary to assign some meaningful attributes to /* empty */ productions. They'll get
                    // the attributes of the next token, even though they don't contain it themselves.
                    $this->startAttributeStack[$stackPos+1] = $startAttributes;
                    $this->endAttributeStack[$stackPos+1] = $endAttributes;
                    $this->lookaheadStartAttributes = $startAttributes;

                    //$this->traceRead($symbol);
                }

                $idx = $this->actionBase[$state] + $symbol;
                if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol)
                     || ($state < $this->YY2TBLSTATE
                         && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $symbol) >= 0
                         && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol))
                    && ($action = $this->action[$idx]) !== $this->defaultAction) {
                    /*
                     * >= YYNLSTATES: shift and reduce
                     * > 0: shift
                     * = 0: accept
                     * < 0: reduce
                     * = -YYUNEXPECTED: error
                     */
                    if ($action > 0) {
                        /* shift */
                        //$this->traceShift($symbol);

                        ++$stackPos;
                        $stateStack[$stackPos] = $state = $action;
                        $this->semStack[$stackPos] = $tokenValue;
                        $this->startAttributeStack[$stackPos] = $startAttributes;
                        $this->endAttributeStack[$stackPos] = $endAttributes;
                        $this->endAttributes = $endAttributes;
                        $symbol = self::SYMBOL_NONE;

                        if ($this->errorState) {
                            --$this->errorState;
                        }

                        if ($action < $this->YYNLSTATES) {
                            continue;
                        }

                        /* $yyn >= YYNLSTATES means shift-and-reduce */
                        $rule = $action - $this->YYNLSTATES;
                    } else {
                        $rule = -$action;
                    }
                } else {
                    $rule = $this->actionDefault[$state];
                }
            }

            for (;;) {
                if ($rule === 0) {
                    /* accept */
                    //$this->traceAccept();
                    return $this->semValue;
                } elseif ($rule !== $this->unexpectedTokenRule) {
                    /* reduce */
                    //$this->traceReduce($rule);

                    try {
                        $this->reduceCallbacks[$rule]($stackPos);
                    } catch (Error $e) {
                        if (-1 === $e->getStartLine() && isset($startAttributes['startLine'])) {
                            $e->setStartLine($startAttributes['startLine']);
                        }

                        $this->emitError($e);
                        // Can't recover from this type of error
                        return null;
                    }

                    /* Goto - shift nonterminal */
                    $lastEndAttributes = $this->endAttributeStack[$stackPos];
                    $stackPos -= $this->ruleToLength[$rule];
                    $nonTerminal = $this->ruleToNonTerminal[$rule];
                    $idx = $this->gotoBase[$nonTerminal] + $stateStack[$stackPos];
                    if ($idx >= 0 && $idx < $this->gotoTableSize && $this->gotoCheck[$idx] === $nonTerminal) {
                        $state = $this->goto[$idx];
                    } else {
                        $state = $this->gotoDefault[$nonTerminal];
                    }

                    ++$stackPos;
                    $stateStack[$stackPos]     = $state;
                    $this->semStack[$stackPos] = $this->semValue;
                    $this->endAttributeStack[$stackPos] = $lastEndAttributes;
                } else {
                    /* error */
                    switch ($this->errorState) {
                        case 0:
                            $msg = $this->getErrorMessage($symbol, $state);
                            $this->emitError(new Error($msg, $startAttributes + $endAttributes));
                            // Break missing intentionally
                        case 1:
                        case 2:
                            $this->errorState = 3;

                            // Pop until error-expecting state uncovered
                            while (!(
                                (($idx = $this->actionBase[$state] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                                || ($state < $this->YY2TBLSTATE
                                    && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                            ) || ($action = $this->action[$idx]) === $this->defaultAction) { // Not totally sure about this
                                if ($stackPos <= 0) {
                                    // Could not recover from error
                                    return null;
                                }
                                $state = $stateStack[--$stackPos];
                                //$this->tracePop($state);
                            }

                            //$this->traceShift($this->errorSymbol);
                            ++$stackPos;
                            $stateStack[$stackPos] = $state = $action;

                            // We treat the error symbol as being empty, so we reset the end attributes
                            // to the end attributes of the last non-error symbol
                            $this->endAttributeStack[$stackPos] = $this->endAttributeStack[$stackPos - 1];
                            $this->endAttributes = $this->endAttributeStack[$stackPos - 1];
                            break;

                        case 3:
                            if ($symbol === 0) {
                                // Reached EOF without recovering from error
                                return null;
                            }

                            //$this->traceDiscard($symbol);
                            $symbol = self::SYMBOL_NONE;
                            break 2;
                    }
                }

                if ($state < $this->YYNLSTATES) {
                    break;
                }

                /* >= YYNLSTATES means shift-and-reduce */
                $rule = $state - $this->YYNLSTATES;
            }
        }

        throw new \RuntimeException('Reached end of parser loop');
    }

    protected function emitError(Error $error) {
        $this->errorHandler->handleError($error);
    }

    /**
     * Format error message including expected tokens.
     *
     * @param int $symbol Unexpected symbol
     * @param int $state  State at time of error
     *
     * @return string Formatted error message
     */
    protected function getErrorMessage(int $symbol, int $state) : string {
        $expectedString = '';
        if ($expected = $this->getExpectedTokens($state)) {
            $expectedString = ', expecting ' . implode(' or ', $expected);
        }

        return 'Syntax error, unexpected ' . $this->symbolToName[$symbol] . $expectedString;
    }

    /**
     * Get limited number of expected tokens in given state.
     *
     * @param int $state State
     *
     * @return string[] Expected tokens. If too many, an empty array is returned.
     */
    protected function getExpectedTokens(int $state) : array {
        $expected = [];

        $base = $this->actionBase[$state];
        foreach ($this->symbolToName as $symbol => $name) {
            $idx = $base + $symbol;
            if ($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
                || $state < $this->YY2TBLSTATE
                && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $symbol) >= 0
                && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
            ) {
                if ($this->action[$idx] !== $this->unexpectedTokenRule
                    && $this->action[$idx] !== $this->defaultAction
                    && $symbol !== $this->errorSymbol
                ) {
                    if (count($expected) === 4) {
                        /* Too many expected tokens */
                        return [];
                    }

                    $expected[] = $name;
                }
            }
        }

        return $expected;
    }

    /*
     * Tracing functions used for debugging the parser.
     */

    /*
    protected function traceNewState($state, $symbol) {
        echo '% State ' . $state
            . ', Lookahead ' . ($symbol == self::SYMBOL_NONE ? '--none--' : $this->symbolToName[$symbol]) . "\n";
    }

    protected function traceRead($symbol) {
        echo '% Reading ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceShift($symbol) {
        echo '% Shift ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceAccept() {
        echo "% Accepted.\n";
    }

    protected function traceReduce($n) {
        echo '% Reduce by (' . $n . ') ' . $this->productions[$n] . "\n";
    }

    protected function tracePop($state) {
        echo '% Recovering, uncovered state ' . $state . "\n";
    }

    protected function traceDiscard($symbol) {
        echo '% Discard ' . $this->symbolToName[$symbol] . "\n";
    }
    */

    /*
     * Helper functions invoked by semantic actions
     */

    /**
     * Moves statements of semicolon-style namespaces into $ns->stmts and checks various error conditions.
     *
     * @param Node\Stmt[] $stmts
     * @return Node\Stmt[]
     */
    protected function handleNamespaces(array $stmts) : array {
        $hasErrored = false;
        $style = $this->getNamespacingStyle($stmts);
        if (null === $style) {
            // not namespaced, nothing to do
            return $stmts;
        } elseif ('brace' === $style) {
            // For braced namespaces we only have to check that there are no invalid statements between the namespaces
            $afterFirstNamespace = false;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    $afterFirstNamespace = true;
                } elseif (!$stmt instanceof Node\Stmt\HaltCompiler
                        && $afterFirstNamespace && !$hasErrored) {
                    $this->emitError(new Error(
                        'No code may exist outside of namespace {}', $stmt->getAttributes()));
                    $hasErrored = true; // Avoid one error for every statement
                }
            }
            return $stmts;
        } else {
            // For semicolon namespaces we have to move the statements after a namespace declaration into ->stmts
            $resultStmts = [];
            $targetStmts =& $resultStmts;
            $lastNs = null;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    if ($lastNs !== null) {
                        $this->fixupNamespaceAttributes($lastNs);
                    }
                    if ($stmt->stmts === null) {
                        $stmt->stmts = [];
                        $targetStmts =& $stmt->stmts;
                        $resultStmts[] = $stmt;
                    } else {
                        // This handles the invalid case of mixed style namespaces
                        $resultStmts[] = $stmt;
                        $targetStmts =& $resultStmts;
                    }
                    $lastNs = $stmt;
                } elseif ($stmt instanceof Node\Stmt\HaltCompiler) {
                    // __halt_compiler() is not moved into the namespace
                    $resultStmts[] = $stmt;
                } else {
                    $targetStmts[] = $stmt;
                }
            }
            if ($lastNs !== null) {
                $this->fixupNamespaceAttributes($lastNs);
            }
            return $resultStmts;
        }
    }

    private function fixupNamespaceAttributes(Node\Stmt\Namespace_ $stmt) {
        // We moved the statements into the namespace node, as such the end of the namespace node
        // needs to be extended to the end of the statements.
        if (empty($stmt->stmts)) {
            return;
        }

        // We only move the builtin end attributes here. This is the best we can do with the
        // knowledge we have.
        $endAttributes = ['endLine', 'endFilePos', 'endTokenPos'];
        $lastStmt = $stmt->stmts[count($stmt->stmts) - 1];
        foreach ($endAttributes as $endAttribute) {
            if ($lastStmt->hasAttribute($endAttribute)) {
                $stmt->setAttribute($endAttribute, $lastStmt->getAttribute($endAttribute));
            }
        }
    }

    /**
     * Determine namespacing style (semicolon or brace)
     *
     * @param Node[] $stmts Top-level statements.
     *
     * @return null|string One of "semicolon", "brace" or null (no namespaces)
     */
    private function getNamespacingStyle(array $stmts) {
        $style = null;
        $hasNotAllowedStmts = false;
        foreach ($stmts as $i => $stmt) {
            if ($stmt instanceof Node\Stmt\Namespace_) {
                $currentStyle = null === $stmt->stmts ? 'semicolon' : 'brace';
                if (null === $style) {
                    $style = $currentStyle;
                    if ($hasNotAllowedStmts) {
                        $this->emitError(new Error(
                            'Namespace declaration statement has to be the very first statement in the script',
                            $stmt->getLine() // Avoid marking the entire namespace as an error
                        ));
                    }
                } elseif ($style !== $currentStyle) {
                    $this->emitError(new Error(
                        'Cannot mix bracketed namespace declarations with unbracketed namespace declarations',
                        $stmt->getLine() // Avoid marking the entire namespace as an error
                    ));
                    // Treat like semicolon style for namespace normalization
                    return 'semicolon';
                }
                continue;
            }

            /* declare(), __halt_compiler() and nops can be used before a namespace declaration */
            if ($stmt instanceof Node\Stmt\Declare_
                || $stmt instanceof Node\Stmt\HaltCompiler
                || $stmt instanceof Node\Stmt\Nop) {
                continue;
            }

            /* There may be a hashbang line at the very start of the file */
            if ($i === 0 && $stmt instanceof Node\Stmt\InlineHTML && preg_match('/\A#!.*\r?\n\z/', $stmt->value)) {
                continue;
            }

            /* Everything else if forbidden before namespace declarations */
            $hasNotAllowedStmts = true;
        }
        return $style;
    }

    /**
     * Fix up parsing of static property calls in PHP 5.
     *
     * In PHP 5 A::$b[c][d] and A::$b[c][d]() have very different interpretation. The former is
     * interpreted as (A::$b)[c][d], while the latter is the same as A::{$b[c][d]}(). We parse the
     * latter as the former initially and this method fixes the AST into the correct form when we
     * encounter the "()".
     *
     * @param  Node\Expr\StaticPropertyFetch|Node\Expr\ArrayDimFetch $prop
     * @param  Node\Arg[] $args
     * @param  array      $attributes
     *
     * @return Expr\StaticCall
     */
    protected function fixupPhp5StaticPropCall($prop, array $args, array $attributes) : Expr\StaticCall {
        if ($prop instanceof Node\Expr\StaticPropertyFetch) {
            $var = new Expr\Variable($prop->name, $prop->name->getAttributes());
            return new Expr\StaticCall($prop->class, $var, $args, $attributes);
        } elseif ($prop instanceof Node\Expr\ArrayDimFetch) {
            $tmp = $prop;
            while ($tmp->var instanceof Node\Expr\ArrayDimFetch) {
                $tmp = $tmp->var;
            }

            /** @var Expr\StaticPropertyFetch $staticProp */
            $staticProp = $tmp->var;

            // Set start attributes to attributes of innermost node
            $tmp = $prop;
            $this->fixupStartAttributes($tmp, $staticProp->name);
            while ($tmp->var instanceof Node\Expr\ArrayDimFetch) {
                $tmp = $tmp->var;
                $this->fixupStartAttributes($tmp, $staticProp->name);
            }

            $result = new Expr\StaticCall($staticProp->class, $prop, $args, $attributes);
            $tmp->var = new Expr\Variable($staticProp->name);
            return $result;
        } else {
            throw new \Exception;
        }
    }

    protected function fixupStartAttributes(Node $to, Node $from) {
        $startAttributes = ['startLine', 'startFilePos', 'startTokenPos'];
        foreach ($startAttributes as $startAttribute) {
            if ($from->hasAttribute($startAttribute)) {
                $to->setAttribute($startAttribute, $from->getAttribute($startAttribute));
            }
        }
    }

    protected function handleBuiltinTypes(Name $name) {
        $scalarTypes = [
            'bool'     => true,
            'int'      => true,
            'float'    => true,
            'string'   => true,
            'iterable' => true,
            'void'     => true,
            'object'   => true,
        ];

        if (!$name->isUnqualified()) {
            return $name;
        }

        $lowerName = strtolower($name->toString());
        if (!isset($scalarTypes[$lowerName])) {
            return $name;
        }

        return new Node\Identifier($lowerName, $name->getAttributes());
    }

    protected static $specialNames = [
        'self'   => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * Get combined start and end attributes at a stack location
     *
     * @param int $pos Stack location
     *
     * @return array Combined start and end attributes
     */
    protected function getAttributesAt(int $pos) : array {
        return $this->startAttributeStack[$pos] + $this->endAttributeStack[$pos];
    }

    protected function parseLNumber($str, $attributes, $allowInvalidOctal = false) {
        try {
            return LNumber::fromString($str, $attributes, $allowInvalidOctal);
        } catch (Error $error) {
            $this->emitError($error);
            // Use dummy value
            return new LNumber(0, $attributes);
        }
    }

    /**
     * Parse a T_NUM_STRING token into either an integer or string node.
     *
     * @param string $str        Number string
     * @param array  $attributes Attributes
     *
     * @return LNumber|String_ Integer or string node.
     */
    protected function parseNumString(string $str, array $attributes) {
        if (!preg_match('/^(?:0|-?[1-9][0-9]*)$/', $str)) {
            return new String_($str, $attributes);
        }

        $num = +$str;
        if (!is_int($num)) {
            return new String_($str, $attributes);
        }

        return new LNumber($num, $attributes);
    }

    protected function checkModifier($a, $b, $modifierPos) {
        // Jumping through some hoops here because verifyModifier() is also used elsewhere
        try {
            Class_::verifyModifier($a, $b);
        } catch (Error $error) {
            $error->setAttributes($this->getAttributesAt($modifierPos));
            $this->emitError($error);
        }
    }

    protected function checkParam(Param $node) {
        if ($node->variadic && null !== $node->default) {
            $this->emitError(new Error(
                'Variadic parameter cannot have a default value',
                $node->default->getAttributes()
            ));
        }
    }

    protected function checkTryCatch(TryCatch $node) {
        if (empty($node->catches) && null === $node->finally) {
            $this->emitError(new Error(
                'Cannot use try without catch or finally', $node->getAttributes()
            ));
        }
    }

    protected function checkNamespace(Namespace_ $node) {
        if ($node->name && isset(self::$specialNames[strtolower($node->name)])) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as namespace name', $node->name),
                $node->name->getAttributes()
            ));
        }

        if (null !== $node->stmts) {
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Namespace_) {
                    $this->emitError(new Error(
                        'Namespace declarations cannot be nested', $stmt->getAttributes()
                    ));
                }
            }
        }
    }

    protected function checkClass(Class_ $node, $namePos) {
        if (null !== $node->name && isset(self::$specialNames[strtolower($node->name)])) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as class name as it is reserved', $node->name),
                $this->getAttributesAt($namePos)
            ));
        }

        if ($node->extends && isset(self::$specialNames[strtolower($node->extends)])) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as class name as it is reserved', $node->extends),
                $node->extends->getAttributes()
            ));
        }

        foreach ($node->implements as $interface) {
            if (isset(self::$specialNames[strtolower($interface)])) {
                $this->emitError(new Error(
                    sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface),
                    $interface->getAttributes()
                ));
            }
        }
    }

    protected function checkInterface(Interface_ $node, $namePos) {
        if (null !== $node->name && isset(self::$specialNames[strtolower($node->name)])) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as class name as it is reserved', $node->name),
                $this->getAttributesAt($namePos)
            ));
        }

        foreach ($node->extends as $interface) {
            if (isset(self::$specialNames[strtolower($interface)])) {
                $this->emitError(new Error(
                    sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface),
                    $interface->getAttributes()
                ));
            }
        }
    }

    protected function checkClassMethod(ClassMethod $node, $modifierPos) {
        if ($node->flags & Class_::MODIFIER_STATIC) {
            switch (strtolower($node->name)) {
                case '__construct':
                    $this->emitError(new Error(
                        sprintf('Constructor %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
                case '__destruct':
                    $this->emitError(new Error(
                        sprintf('Destructor %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
                case '__clone':
                    $this->emitError(new Error(
                        sprintf('Clone method %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
            }
        }
    }

    protected function checkClassConst(ClassConst $node, $modifierPos) {
        if ($node->flags & Class_::MODIFIER_STATIC) {
            $this->emitError(new Error(
                "Cannot use 'static' as constant modifier",
                $this->getAttributesAt($modifierPos)));
        }
        if ($node->flags & Class_::MODIFIER_ABSTRACT) {
            $this->emitError(new Error(
                "Cannot use 'abstract' as constant modifier",
                $this->getAttributesAt($modifierPos)));
        }
        if ($node->flags & Class_::MODIFIER_FINAL) {
            $this->emitError(new Error(
                "Cannot use 'final' as constant modifier",
                $this->getAttributesAt($modifierPos)));
        }
    }

    protected function checkProperty(Property $node, $modifierPos) {
        if ($node->flags & Class_::MODIFIER_ABSTRACT) {
            $this->emitError(new Error('Properties cannot be declared abstract',
                $this->getAttributesAt($modifierPos)));
        }

        if ($node->flags & Class_::MODIFIER_FINAL) {
            $this->emitError(new Error('Properties cannot be declared final',
                $this->getAttributesAt($modifierPos)));
        }
    }

    protected function checkUseUse(UseUse $node, $namePos) {
        if ('self' === strtolower($node->alias) || 'parent' === strtolower($node->alias)) {
            $this->emitError(new Error(
                sprintf(
                    'Cannot use %s as %s because \'%2$s\' is a special class name',
                    $node->name, $node->alias
                ),
                $this->getAttributesAt($namePos)
            ));
        }
    }
}
