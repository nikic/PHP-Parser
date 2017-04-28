<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

abstract class PrettyPrinterAbstract
{
    const FIXUP_PREC_LEFT       = 0; // LHS operand affected by precedence
    const FIXUP_PREC_RIGHT      = 1; // RHS operand affected by precedence
    const FIXUP_CALL_LHS        = 2; // LHS of call
    const FIXUP_DEREF_LHS       = 3; // LHS of dereferencing operation
    const FIXUP_BRACED_NAME     = 4; // Name operand that may require bracing
    const FIXUP_VAR_BRACED_NAME = 5; // Name operand that may require ${} bracing
    const FIXUP_ENCAPSED        = 6; // Encapsed string part

    protected $precedenceMap = array(
        // [precedence, associativity]
        // where for precedence -1 is %left, 0 is %nonassoc and 1 is %right
        'Expr_BinaryOp_Pow'            => array(  0,  1),
        'Expr_BitwiseNot'              => array( 10,  1),
        'Expr_PreInc'                  => array( 10,  1),
        'Expr_PreDec'                  => array( 10,  1),
        'Expr_PostInc'                 => array( 10, -1),
        'Expr_PostDec'                 => array( 10, -1),
        'Expr_UnaryPlus'               => array( 10,  1),
        'Expr_UnaryMinus'              => array( 10,  1),
        'Expr_Cast_Int'                => array( 10,  1),
        'Expr_Cast_Double'             => array( 10,  1),
        'Expr_Cast_String'             => array( 10,  1),
        'Expr_Cast_Array'              => array( 10,  1),
        'Expr_Cast_Object'             => array( 10,  1),
        'Expr_Cast_Bool'               => array( 10,  1),
        'Expr_Cast_Unset'              => array( 10,  1),
        'Expr_ErrorSuppress'           => array( 10,  1),
        'Expr_Instanceof'              => array( 20,  0),
        'Expr_BooleanNot'              => array( 30,  1),
        'Expr_BinaryOp_Mul'            => array( 40, -1),
        'Expr_BinaryOp_Div'            => array( 40, -1),
        'Expr_BinaryOp_Mod'            => array( 40, -1),
        'Expr_BinaryOp_Plus'           => array( 50, -1),
        'Expr_BinaryOp_Minus'          => array( 50, -1),
        'Expr_BinaryOp_Concat'         => array( 50, -1),
        'Expr_BinaryOp_ShiftLeft'      => array( 60, -1),
        'Expr_BinaryOp_ShiftRight'     => array( 60, -1),
        'Expr_BinaryOp_Smaller'        => array( 70,  0),
        'Expr_BinaryOp_SmallerOrEqual' => array( 70,  0),
        'Expr_BinaryOp_Greater'        => array( 70,  0),
        'Expr_BinaryOp_GreaterOrEqual' => array( 70,  0),
        'Expr_BinaryOp_Equal'          => array( 80,  0),
        'Expr_BinaryOp_NotEqual'       => array( 80,  0),
        'Expr_BinaryOp_Identical'      => array( 80,  0),
        'Expr_BinaryOp_NotIdentical'   => array( 80,  0),
        'Expr_BinaryOp_Spaceship'      => array( 80,  0),
        'Expr_BinaryOp_BitwiseAnd'     => array( 90, -1),
        'Expr_BinaryOp_BitwiseXor'     => array(100, -1),
        'Expr_BinaryOp_BitwiseOr'      => array(110, -1),
        'Expr_BinaryOp_BooleanAnd'     => array(120, -1),
        'Expr_BinaryOp_BooleanOr'      => array(130, -1),
        'Expr_BinaryOp_Coalesce'       => array(140,  1),
        'Expr_Ternary'                 => array(150, -1),
        // parser uses %left for assignments, but they really behave as %right
        'Expr_Assign'                  => array(160,  1),
        'Expr_AssignRef'               => array(160,  1),
        'Expr_AssignOp_Plus'           => array(160,  1),
        'Expr_AssignOp_Minus'          => array(160,  1),
        'Expr_AssignOp_Mul'            => array(160,  1),
        'Expr_AssignOp_Div'            => array(160,  1),
        'Expr_AssignOp_Concat'         => array(160,  1),
        'Expr_AssignOp_Mod'            => array(160,  1),
        'Expr_AssignOp_BitwiseAnd'     => array(160,  1),
        'Expr_AssignOp_BitwiseOr'      => array(160,  1),
        'Expr_AssignOp_BitwiseXor'     => array(160,  1),
        'Expr_AssignOp_ShiftLeft'      => array(160,  1),
        'Expr_AssignOp_ShiftRight'     => array(160,  1),
        'Expr_AssignOp_Pow'            => array(160,  1),
        'Expr_YieldFrom'               => array(165,  1),
        'Expr_Print'                   => array(168,  1),
        'Expr_BinaryOp_LogicalAnd'     => array(170, -1),
        'Expr_BinaryOp_LogicalXor'     => array(180, -1),
        'Expr_BinaryOp_LogicalOr'      => array(190, -1),
        'Expr_Include'                 => array(200, -1),
    );

    /** @var string Token placed before newline to ensure it is not indented. */
    protected $noIndentToken;
    /** @var string Token placed at end of doc string to ensure it is followed by a newline. */
    protected $docStringEndToken;
    /** @var bool Whether semicolon namespaces can be used (i.e. no global namespace is used) */
    protected $canUseSemicolonNamespaces;
    /** @var array Pretty printer options */
    protected $options;

    /** @var array Original tokens for use in format-preserving pretty print */
    protected $origTokens;
    /** @var int Current indentation level during format-preserving pretty pting */
    protected $indentLevel;
    /** @var bool[] Map determining whether a certain character is a label character */
    protected $labelCharMap;
    /**
     * @var int[][] Map from token types and subnode names to FIXUP_* constants. This is used
     *              during format-preserving prints to place additional parens/braces if necessary.
     */
    protected $fixupMap;
    /**
     * @var int[][] Map from "{$node->getType()}->{$subNode}" to ['left' => $l, 'right' => $r],
     *              where $l and $r specify the token type that needs to be stripped when removing
     *              this node.
     */
    protected $removalMap;
    protected $insertionMap;

    /**
     * Creates a pretty printer instance using the given options.
     *
     * Supported options:
     *  * bool $shortArraySyntax = false: Whether to use [] instead of array() as the default array
     *                                    syntax, if the node does not specify a format.
     *
     * @param array $options Dictionary of formatting options
     */
    public function __construct(array $options = []) {
        $this->noIndentToken = '_NO_INDENT_' . mt_rand();
        $this->docStringEndToken = '_DOC_STRING_END_' . mt_rand();

        $defaultOptions = ['shortArraySyntax' => false];
        $this->options = $options + $defaultOptions;
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts) : string {
        $this->preprocessNodes($stmts);

        return ltrim($this->handleMagicTokens($this->pStmts($stmts, false)));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node) : string {
        return $this->handleMagicTokens($this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts) : string {
        if (!$stmts) {
            return "<?php\n\n";
        }

        $p = "<?php\n\n" . $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        return $p;
    }

    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes) {
        /* We can use semicolon-namespaces unless there is a global namespace declaration */
        $this->canUseSemicolonNamespaces = true;
        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_ && null === $node->name) {
                $this->canUseSemicolonNamespaces = false;
            }
        }
    }

    /**
     * Handles (and removes) no-indent and doc-string-end tokens.
     *
     * @param string $str
     * @return string
     */
    protected function handleMagicTokens(string $str) : string {
        // Drop no-indent tokens
        $str = str_replace($this->noIndentToken, '', $str);

        // Replace doc-string-end tokens with nothing or a newline
        $str = str_replace($this->docStringEndToken . ";\n", ";\n", $str);
        $str = str_replace($this->docStringEndToken, "\n", $str);

        return $str;
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param Node[] $nodes  Array of nodes
     * @param bool   $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, bool $indent = true) : string {
        $result = '';
        foreach ($nodes as $node) {
            $comments = $node->getAttribute('comments', array());
            if ($comments) {
                $result .= "\n" . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $result .= "\n" . $this->p($node);
        }

        if ($indent) {
            return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n    ", $result);
        } else {
            return $result;
        }
    }

    /**
     * Pretty-print an infix operation while taking precedence into account.
     *
     * @param string $type           Node type of operator
     * @param Node   $leftNode       Left-hand side node
     * @param string $operatorString String representation of the operator
     * @param Node   $rightNode      Right-hand side node
     *
     * @return string Pretty printed infix operation
     */
    protected function pInfixOp(string $type, Node $leftNode, string $operatorString, Node $rightNode) : string {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
             . $operatorString
             . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }

    /**
     * Pretty-print a prefix operation while taking precedence into account.
     *
     * @param string $type           Node type of operator
     * @param string $operatorString String representation of the operator
     * @param Node   $node           Node
     *
     * @return string Pretty printed prefix operation
     */
    protected function pPrefixOp(string $type, string $operatorString, Node $node) : string {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }

    /**
     * Pretty-print a postfix operation while taking precedence into account.
     *
     * @param string $type           Node type of operator
     * @param string $operatorString String representation of the operator
     * @param Node   $node           Node
     *
     * @return string Pretty printed postfix operation
     */
    protected function pPostfixOp(string $type, Node $node, string $operatorString) : string {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $this->pPrec($node, $precedence, $associativity, -1) . $operatorString;
    }

    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param Node $node                Node to pretty print
     * @param int  $parentPrecedence    Precedence of the parent operator
     * @param int  $parentAssociativity Associativity of parent operator
     *                                  (-1 is left, 0 is nonassoc, 1 is right)
     * @param int  $childPosition       Position of the node relative to the operator
     *                                  (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    protected function pPrec(Node $node, int $parentPrecedence, int $parentAssociativity, int $childPosition) : string {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->p($node) . ')';
            }
        }

        return $this->p($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param string $glue  Character to implode with
     *
     * @return string Imploded pretty printed nodes
     */
    protected function pImplode(array $nodes, string $glue = '') : string {
        $pNodes = array();
        foreach ($nodes as $node) {
            if (null === $node) {
                $pNodes[] = '';
            } else {
                $pNodes[] = $this->p($node);
            }
        }

        return implode($glue, $pNodes);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes) : string {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Pretty prints a comma-separated list of nodes in multiline style, including comments.
     *
     * The result includes a leading newline and one level of indentation (same as pStmts).
     *
     * @param Node[] $nodes         Array of Nodes to be printed
     * @param bool   $trailingComma Whether to use a trailing comma
     *
     * @return string Comma separated pretty printed nodes in multiline style
     */
    protected function pCommaSeparatedMultiline(array $nodes, bool $trailingComma) : string {
        $result = '';
        $lastIdx = count($nodes) - 1;
        foreach ($nodes as $idx => $node) {
            if ($node !== null) {
                $comments = $node->getAttribute('comments', array());
                if ($comments) {
                    $result .= "\n" . $this->pComments($comments);
                }

                $result .= "\n" . $this->p($node);
            } else {
                $result .= "\n";
            }
            if ($trailingComma || $idx !== $lastIdx) {
                $result .= ',';
            }
        }

        return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n    ", $result);
    }

    /**
     * Signals the pretty printer that a string shall not be indented.
     *
     * @param string $string Not to be indented string
     *
     * @return string String marked with $this->noIndentToken's.
     */
    protected function pNoIndent(string $string) : string {
        return str_replace("\n", "\n" . $this->noIndentToken, $string);
    }

    /**
     * Prints reformatted text of the passed comments.
     *
     * @param Comment[] $comments List of comments
     *
     * @return string Reformatted text of comments
     */
    protected function pComments(array $comments) : string {
        $formattedComments = [];

        foreach ($comments as $comment) {
            $formattedComments[] = $comment->getReformattedText();
        }

        return implode("\n", $formattedComments);
    }

    /**
     * Perform a format-preserving pretty print of an AST.
     *
     * The format preservation is best effort. For some changes to the AST the formatting will not
     * be preserved (at least not locally).
     *
     * In order to use this method a number of prerequisites must be satisfied:
     *  * The startTokenPos and endTokenPos attributes in the lexer must be enabled.
     *  * The CloningVisitor must be run on the AST prior to modification.
     *  * The original tokens must be provided, using the getTokens() method on the lexer.
     *
     * @param Node[] $stmts      Modified AST with links to original AST
     * @param Node[] $origStmts  Original AST with token offset information
     * @param array  $origTokens Tokens of the original code
     *
     * @return string
     */
    public function printFormatPreserving(array $stmts, array $origStmts, array $origTokens) : string {
        $this->initializeLabelCharMap();
        $this->initializeFixupMap();
        $this->initializeRemovalMap();
        $this->initializeInsertionMap();

        $this->origTokens = $origTokens;
        $this->indentLevel = 0;

        $this->preprocessNodes($stmts);

        $pos = 0;
        $result = $this->pArray($stmts, $origStmts, $pos, 0, null);
        if (null !== $result) {
            $result .= $this->getTokenCode($pos, count($this->origTokens), 0);
        } else {
            // Fallback
            // TODO Add <?php properly
            $result = "<?php\n" . $this->pStmts($stmts, false);
        }

        return ltrim($this->handleMagicTokens($result));
    }

    protected function pFallback(Node $node) {
        return $this->{'p' . $node->getType()}($node);
    }

    /**
     * Pretty prints a node.
     *
     * This method also handles formatting preservation for nodes.
     *
     * @param Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(Node $node) : string {
        // No orig tokens means this is a normal pretty print without preservation of formatting
        if (!$this->origTokens) {
            return $this->{'p' . $node->getType()}($node);
        }

        /** @var Node $origNode */
        $origNode = $node->getAttribute('origNode');
        if (null === $origNode) {
            return $this->pFallback($node);
        }

        if (get_class($node) != get_class($origNode)) {
            // Shouldn't happen
            return $this->pFallback($node);
        }

        $startPos = $origNode->getAttribute('startTokenPos', -1);
        $endPos = $origNode->getAttribute('endTokenPos', -1);
        if ($startPos < 0 || $endPos < 0) {
            // Shouldn't happen
            return $this->pFallback($node);
        }

        if ($node instanceof Expr\New_ && $node->class instanceof Stmt\Class_) {
            // For anonymous classes the new and class nodes are intermixed, this would require
            // special handling (or maybe a new node type just for this?)
            return $this->pFallback($node);
        }

        $indentAdjustment = $this->indentLevel - $this->getIndentationBefore($startPos);

        $type = $node->getType();
        $fixupInfo = isset($this->fixupMap[$type]) ? $this->fixupMap[$type] : null;

        $result = '';
        $pos = $startPos;
        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->$subNodeName;
            $origSubNode = $origNode->$subNodeName;

            if ((!$subNode instanceof Node && $subNode !== null)
                || (!$origSubNode instanceof Node && $origSubNode !== null)
            ) {
                if ($subNode === $origSubNode) {
                    // Unchanged, can reuse old code
                    continue;
                }

                if (is_array($subNode) && is_array($origSubNode)) {
                    // Array subnode changed, we might be able to reconstruct it
                    $fixup = isset($fixupInfo[$subNodeName]) ? $fixupInfo[$subNodeName] : null;
                    $listResult = $this->pArray(
                        $subNode, $origSubNode, $pos, $indentAdjustment, $fixup
                    );
                    if (null === $listResult) {
                        return $this->pFallback($node);
                    }

                    $result .= $listResult;
                    continue;
                }

                // If a non-node, non-array subnode changed, we don't be able to do a partial
                // reconstructions, as we don't have enough offset information. Pretty print the
                // whole node instead.
                return $this->pFallback($node);
            }

            $extraLeft = '';
            $extraRight = '';
            if ($origSubNode !== null) {
                $subStartPos = $origSubNode->getAttribute('startTokenPos', -1);
                $subEndPos = $origSubNode->getAttribute('endTokenPos', -1);
                if ($subStartPos < 0 || $subEndPos < 0) {
                    // Shouldn't happen
                    return $this->pFallback($node);
                }
            } else {
                if ($subNode === null) {
                    // Both null, nothing to do
                    continue;
                }

                // A node has been inserted, check if we have insertion information for it
                $key = $type . '->' . $subNodeName;
                if (!isset($this->insertionMap[$key])) {
                    return $this->pFallback($node);
                }

                list($findToken, $extraLeft, $extraRight) = $this->insertionMap[$key];
                if (null !== $findToken) {
                    $subStartPos = $this->findRight($pos, $findToken) + 1;
                } else {
                    $subStartPos = $pos;
                }
                if (null === $extraLeft && null !== $extraRight) {
                    // If inserting on the right only, skipping whitespace looks better
                    $subStartPos = $this->skipRightWhitespace($subStartPos);
                }
                $subEndPos = $subStartPos - 1;
            }

            if (null === $subNode) {
                // A node has been removed, check if we have removal information for it
                $key = $type . '->' . $subNodeName;
                if (!isset($this->removalMap[$key])) {
                    return $this->pFallback($node);
                }

                // Adjust positions to account for additional tokens that must be skipped
                $removalInfo = $this->removalMap[$key];
                if (isset($removalInfo['left'])) {
                    $subStartPos = $this->skipLeft($subStartPos - 1, $removalInfo['left']) + 1;
                }
                if (isset($removalInfo['right'])) {
                    $subEndPos = $this->skipRight($subEndPos + 1, $removalInfo['right']) - 1;
                }
            }

            $result .= $this->getTokenCode($pos, $subStartPos, $indentAdjustment);

            if (null !== $subNode) {
                $result .= $extraLeft;

                $origIndentLevel = $this->indentLevel;
                $this->indentLevel = $this->getIndentationBefore($subStartPos) + $indentAdjustment;

                // If it's the same node that was previously in this position, it certainly doesn't
                // need fixup. It's important to check this here, because our fixup checks are more
                // conservative than strictly necessary.
                if (isset($fixupInfo[$subNodeName])
                    && $subNode->getAttribute('origNode') !== $origSubNode
                ) {
                    $fixup = $fixupInfo[$subNodeName];
                    $res = $this->pFixup($fixup, $subNode, $type, $subStartPos, $subEndPos);
                } else {
                    $res = $this->p($subNode);
                }

                $this->safeAppend($result, $res);
                $this->indentLevel = $origIndentLevel;

                $result .= $extraRight;
            }

            $pos = $subEndPos + 1;
        }

        $result .= $this->getTokenCode($pos, $endPos + 1, $indentAdjustment);
        return $result;
    }

    /**
     * Perform a format-preserving pretty print of an array
     *
     * @param array    $nodes            New nodes
     * @param array    $origNodes        Original nodes
     * @param int      $pos              Current token position (updated by reference)
     * @param int      $indentAdjustment Adjustment for indentation
     * @param null|int $fixup            Fixup information for array item nodes
     *
     * @return null|string Result of pretty print or null if cannot preserve formatting
     */
    protected function pArray(array $nodes, array $origNodes, int &$pos, int $indentAdjustment, $fixup) {
        $len = count($nodes);
        $origLen = count($origNodes);
        if ($len !== $origLen) {
            return null;
        }

        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $arrItem = $nodes[$i];
            $origArrItem = $origNodes[$i];
            if ($arrItem === $origArrItem) {
                // Unchanged, can reuse old code
                continue;
            }

            if (!$arrItem instanceof Node || !$origArrItem instanceof Node) {
                // We can only handle arrays of nodes meaningfully
                return null;
            }

            $itemStartPos = $origArrItem->getAttribute('startTokenPos', -1);
            $itemEndPos = $origArrItem->getAttribute('endTokenPos', -1);
            if ($itemStartPos < 0 || $itemEndPos < 0) {
                // Shouldn't happen
                return null;
            }

            if ($itemEndPos < $itemStartPos) {
                // End can be before start for Nop nodes, because offsets refer to non-whitespace
                // locations, which for an "empty" node might result in an inverted order.
                assert($origArrItem instanceof Stmt\Nop);
                continue;
            }

            $result .= $this->getTokenCode($pos, $itemStartPos, $indentAdjustment);

            $origIndentLevel = $this->indentLevel;
            $this->indentLevel = $this->getIndentationBefore($itemStartPos) + $indentAdjustment;

            if (null !== $fixup && $arrItem->getAttribute('origNode') !== $origArrItem) {
                $res = $this->pFixup($fixup, $arrItem, null, $itemStartPos, $itemEndPos);
            } else {
                $res = $this->p($arrItem);
            }
            $this->safeAppend($result, $res);

            $this->indentLevel = $origIndentLevel;
            $pos = $itemEndPos + 1;
        }
        return $result;
    }

    /**
     * Print node with fixups.
     *
     * Fixups here refer to the addition of extra parentheses, braces or other characters, that
     * are required to preserve program semantics in a certain context (e.g. to maintain precedence
     * or because only certain expressions are allowed in certain places).
     *
     * @param int         $fixup       Fixup type
     * @param Node        $subNode     Subnode to print
     * @param string|null $parentType  Type of parent node
     * @param int         $subStartPos Original start pos of subnode
     * @param int         $subEndPos   Original end pos of subnode
     *
     * @return string Result of fixed-up print of subnode
     */
    protected function pFixup(int $fixup, Node $subNode, $parentType, int $subStartPos, int $subEndPos) : string {
        switch ($fixup) {
            case self::FIXUP_PREC_LEFT:
            case self::FIXUP_PREC_RIGHT:
                if (!$this->haveParens($subStartPos, $subEndPos)) {
                    list($precedence, $associativity) = $this->precedenceMap[$parentType];
                    return $this->pPrec($subNode, $precedence, $associativity,
                        $fixup === self::FIXUP_PREC_LEFT ? -1 : 1);
                }
                break;
            case self::FIXUP_CALL_LHS:
                if ($this->callLhsRequiresParens($subNode)
                    && !$this->haveParens($subStartPos, $subEndPos)
                ) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_DEREF_LHS:
                if ($this->dereferenceLhsRequiresParens($subNode)
                    && !$this->haveParens($subStartPos, $subEndPos)
                ) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_BRACED_NAME:
            case self::FIXUP_VAR_BRACED_NAME:
                if ($subNode instanceof Expr
                    && !$this->haveBraces($subStartPos, $subEndPos)
                ) {
                    return ($fixup === self::FIXUP_VAR_BRACED_NAME ? '$' : '')
                        . '{' . $this->p($subNode) . '}';
                }
                break;
            case self::FIXUP_ENCAPSED:
                if (!$subNode instanceof Scalar\EncapsedStringPart
                    && !$this->haveBraces($subStartPos, $subEndPos)
                ) {
                    return '{' . $this->p($subNode) . '}';
                }
                break;
            default:
                throw new \Exception('Cannot happen');
        }

        // Nothing special to do
        return $this->p($subNode);
    }

    /**
     * Appends to a string, ensuring whitespace between label characters.
     *
     * Example: "echo" and "$x" result in "echo$x", but "echo" and "x" result in "echo x".
     * Without safeAppend the result would be "echox", which does not preserve semantics.
     *
     * @param string $str
     * @param string $append
     */
    protected function safeAppend(string &$str, string $append) {
        // $append must not be empty in this function
        if ($str === "") {
            $str = $append;
            return;
        }

        if (!$this->labelCharMap[$append[0]]
                || !$this->labelCharMap[$str[\strlen($str) - 1]]) {
            $str .= $append;
        } else {
            $str .= " " . $append;
        }
    }

    /**
     * Get indentation before token position
     *
     * @param int $pos Token position
     *
     * @return int Indentation depth (in spaces)
     */
    protected function getIndentationBefore(int $pos) : int {
        $tokens = $this->origTokens;
        $indent = 0;
        $pos--;
        for (; $pos >= 0; $pos--) {
            if ($tokens[$pos][0] !== T_WHITESPACE) {
                $indent = 0;
                continue;
            }
            $content = $tokens[$pos][1];
            $newlinePos = \strrpos($content, "\n");
            if (false !== $newlinePos) {
                $indent += \strlen($content) - $newlinePos - 1;
                return $indent;
            }

            $indent += \strlen($content);
        }
        return $indent;
    }

    /**
     * Whether the fiven position is immediately surrounded by parenthesis.
     *
     * @param int $startPos Start position
     * @param int $endPos   End position
     *
     * @return bool
     */
    protected function haveParens(int $startPos, int $endPos) : bool {
        return $this->haveTokenImmediativelyBefore($startPos, '(')
            && $this->haveTokenImmediatelyAfter($endPos, ')');
    }

    /**
     * Whether the fiven position is immediately surrounded by braces.
     *
     * @param int $startPos Start position
     * @param int $endPos   End position
     *
     * @return bool
     */
    protected function haveBraces(int $startPos, int $endPos) : bool {
        return $this->haveTokenImmediativelyBefore($startPos, '{')
            && $this->haveTokenImmediatelyAfter($endPos, '}');
    }

    /**
     * Check whether the position is directly preceded by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int        $pos               Position before which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    protected function haveTokenImmediativelyBefore(int $pos, $expectedTokenType) : bool {
        $tokens = $this->origTokens;
        $pos--;
        for (; $pos >= 0; $pos--) {
            $tokenType = $tokens[$pos][0];
            if ($tokenType === $expectedTokenType) {
                return true;
            }
            if ($tokenType !== T_WHITESPACE
                    && $tokenType !== T_COMMENT && $tokenType !== T_DOC_COMMENT) {
                break;
            }
        }
        return false;
    }

    /**
     * Check whether the position is directly followed by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int        $pos               Position after which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    protected function haveTokenImmediatelyAfter(int $pos, $expectedTokenType) : bool {
        $tokens = $this->origTokens;
        $pos++;
        for (; $pos < \count($tokens); $pos++) {
            $tokenType = $tokens[$pos][0];
            if ($tokenType === $expectedTokenType) {
                return true;
            }
            if ($tokenType !== T_WHITESPACE
                && $tokenType !== T_COMMENT && $tokenType !== T_DOC_COMMENT) {
                break;
            }
        }
        return false;
    }

    /**
     * Get the code corresponding to a token offset range, optionally adjusted for indentation.
     *
     * @param int $from   Token start position (inclusive)
     * @param int $to     Token end position (exclusive)
     * @param int $indent By how much the code should be indented (can be negative as well)
     *
     * @return string Code corresponding to token range, adjusted for indentation
     */
    protected function getTokenCode(int $from, int $to, int $indent) : string {
        $tokens = $this->origTokens;
        $result = '';
        for ($pos = $from; $pos < $to; $pos++) {
            $token = $tokens[$pos];
            if (\is_array($token)) {
                $type = $token[0];
                $content = $token[1];
                if ($type === T_CONSTANT_ENCAPSED_STRING || $type === T_ENCAPSED_AND_WHITESPACE) {
                    $result .= $this->pNoIndent($content);
                } else {
                    // TODO Handle non-space indentation
                    if ($indent < 0) {
                        $result .= str_replace("\n" . str_repeat(" ", -$indent), "\n", $content);
                    } else if ($indent > 0) {
                        $result .= str_replace("\n", "\n" . str_repeat(" ", $indent), $content);
                    } else {
                        $result .= $content;
                    }
                }
            } else {
                $result .= $token;
            }
        }
        return $result;
    }

    protected function skipLeft($pos, $skipTokenType) {
        $tokens = $this->origTokens;

        $pos = $this->skipLeftWhitespace($pos);
        if ($skipTokenType === T_WHITESPACE) {
            return $pos;
        }

        if ($tokens[$pos][0] !== $skipTokenType) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos--;

        return $this->skipLeftWhitespace($pos);
    }

    protected function skipRight($pos, $skipTokenType) {
        $tokens = $this->origTokens;

        $pos = $this->skipRightWhitespace($pos);
        if ($skipTokenType === T_WHITESPACE) {
            return $pos;
        }

        if ($tokens[$pos][0] !== $skipTokenType) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos++;

        return $this->skipRightWhitespace($pos);
    }

    protected function skipLeftWhitespace($pos) {
        $tokens = $this->origTokens;
        for (; $pos >= 0; $pos--) {
            $type = $tokens[$pos][0];
            if ($type !== T_WHITESPACE && $type !== T_COMMENT && $type !== T_DOC_COMMENT) {
                break;
            }
        }
        return $pos;
    }

    protected function skipRightWhitespace($pos) {
        $tokens = $this->origTokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            $type = $tokens[$pos][0];
            if ($type !== T_WHITESPACE && $type !== T_COMMENT && $type !== T_DOC_COMMENT) {
                break;
            }
        }
        return $pos;
    }

    protected function findRight($pos, $findTokenType) {
        $tokens = $this->origTokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            $type = $tokens[$pos][0];
            if ($type === $findTokenType) {
                return $pos;
            }
        }
        return -1;
    }

    /**
     * Determines whether the LHS of a call must be wrapped in parenthesis.
     *
     * @param Node $node LHS of a call
     *
     * @return bool Whether parentheses are required
     */
    protected function callLhsRequiresParens(Node $node) : bool {
        return !($node instanceof Node\Name
            || $node instanceof Expr\Variable
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_);
    }

    /**
     * Determines whether the LHS of a dereferencing operation must be wrapped in parenthesis.
     *
     * @param Node $node LHS of dereferencing operation
     *
     * @return bool Whether parentheses are required
     */
    protected function dereferenceLhsRequiresParens(Node $node) : bool {
        return !($node instanceof Expr\Variable
            || $node instanceof Node\Name
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\StaticPropertyFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
            || $node instanceof Scalar\String_
            || $node instanceof Expr\ConstFetch
            || $node instanceof Expr\ClassConstFetch);
    }

    /**
     * Lazily initializes label char map.
     *
     * The label char map determines whether a certain character may occur in a label.
     */
    protected function initializeLabelCharMap() {
        if ($this->labelCharMap) return;

        $this->labelCharMap = [];
        for ($i = 0; $i < 256; $i++) {
            // Since PHP 7.1 The lower range is 0x80. However, we also want to support code for
            // older versions.
            $this->labelCharMap[chr($i)] = $i >= 0x7f || ctype_alnum($i);
        }
    }

    /**
     * Lazily initializes fixup map.
     *
     * The fixup map is used to determine whether a certain subnode of a certain node may require
     * some kind of "fixup" operation, e.g. the addition of parenthesis or braces.
     */
    protected function initializeFixupMap() {
        if ($this->fixupMap) return;

        $this->fixupMap = [
            'Expr_PreInc' => ['var' => self::FIXUP_PREC_RIGHT],
            'Expr_PreDec' => ['var' => self::FIXUP_PREC_RIGHT],
            'Expr_PostInc' => ['var' => self::FIXUP_PREC_LEFT],
            'Expr_PostDec' => ['var' => self::FIXUP_PREC_LEFT],
            'Expr_Instanceof' => [
                'expr' => self::FIXUP_PREC_LEFT,
                'class' => self::FIXUP_PREC_RIGHT,
            ],
            'Expr_Ternary' => [
                'cond' => self::FIXUP_PREC_LEFT,
                'else' => self::FIXUP_PREC_RIGHT,
            ],

            'Expr_FuncCall' => ['name' => self::FIXUP_CALL_LHS],
            'Expr_StaticCall' => ['class' => self::FIXUP_DEREF_LHS],
            'Expr_ArrayDimFetch' => ['var' => self::FIXUP_DEREF_LHS],
            'Expr_MethodCall' => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            'Expr_StaticPropertyFetch' => [
                'class' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_VAR_BRACED_NAME,
            ],
            'Expr_PropertyFetch' => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            'Scalar_Encapsed' => [
                'parts' => self::FIXUP_ENCAPSED,
            ],
        ];

        $binaryOps = [
            'Expr_BinaryOp_Pow', 'Expr_BinaryOp_Mul', 'Expr_BinaryOp_Div', 'Expr_BinaryOp_Mod',
            'Expr_BinaryOp_Plus', 'Expr_BinaryOp_Minus', 'Expr_BinaryOp_Concat',
            'Expr_BinaryOp_ShiftLeft', 'Expr_BinaryOp_ShiftRight', 'Expr_BinaryOp_Smaller',
            'Expr_BinaryOp_SmallerOrEqual', 'Expr_BinaryOp_Greater', 'Expr_BinaryOp_GreaterOrEqual',
            'Expr_BinaryOp_Equal', 'Expr_BinaryOp_NotEqual', 'Expr_BinaryOp_Identical',
            'Expr_BinaryOp_NotIdentical', 'Expr_BinaryOp_Spaceship', 'Expr_BinaryOp_BitwiseAnd',
            'Expr_BinaryOp_BitwiseXor', 'Expr_BinaryOp_BitwiseOr', 'Expr_BinaryOp_BooleanAnd',
            'Expr_BinaryOp_BooleanOr', 'Expr_BinaryOp_Coalesce', 'Expr_BinaryOp_LogicalAnd',
            'Expr_BinaryOp_LogicalXor', 'Expr_BinaryOp_LogicalOr',
        ];
        foreach ($binaryOps as $binaryOp) {
            $this->fixupMap[$binaryOp] = [
                'left' => self::FIXUP_PREC_LEFT,
                'right' => self::FIXUP_PREC_RIGHT
            ];
        }

        $assignOps = [
            'Expr_Assign', 'Expr_AssignRef', 'Expr_AssignOp_Plus', 'Expr_AssignOp_Minus',
            'Expr_AssignOp_Mul', 'Expr_AssignOp_Div', 'Expr_AssignOp_Concat', 'Expr_AssignOp_Mod',
            'Expr_AssignOp_BitwiseAnd', 'Expr_AssignOp_BitwiseOr', 'Expr_AssignOp_BitwiseXor',
            'Expr_AssignOp_ShiftLeft', 'Expr_AssignOp_ShiftRight', 'Expr_AssignOp_Pow',
        ];
        foreach ($assignOps as $assignOp) {
            $this->fixupMap[$assignOp] = [
                'var' => self::FIXUP_PREC_LEFT,
                'expr' => self::FIXUP_PREC_RIGHT,
            ];
        }

        $prefixOps = [
            'Expr_BitwiseNot', 'Expr_BooleanNot', 'Expr_UnaryPlus', 'Expr_UnaryMinus',
            'Expr_Cast_Int', 'Expr_Cast_Double', 'Expr_Cast_String', 'Expr_Cast_Array',
            'Expr_Cast_Object', 'Expr_Cast_Bool', 'Expr_Cast_Unset', 'Expr_ErrorSuppress',
            'Expr_YieldFrom', 'Expr_Print', 'Expr_Include',
        ];
        foreach ($prefixOps as $prefixOp) {
            $this->fixupMap[$prefixOp] = ['expr' => self::FIXUP_PREC_RIGHT];
        }
    }

    /**
     * Lazily initializes the removal map.
     *
     * The removal map is used to determine which additional tokens should be returned when a
     * certain node is replaced by null.
     */
    protected function initializeRemovalMap() {
        if ($this->removalMap) return;

        $stripBoth = ['left' => T_WHITESPACE, 'right' => T_WHITESPACE];
        $stripLeft = ['left' => T_WHITESPACE];
        $stripRight = ['right' => T_WHITESPACE];
        $stripDoubleArrow = ['right' => T_DOUBLE_ARROW];
        $stripColon = ['left' => ':'];
        $stripEquals = ['left' => '='];
        $this->removalMap = [
            'Expr_ArrayDimFetch->dim' => $stripBoth,
            'Expr_ArrayItem->key' => $stripDoubleArrow,
            'Expr_Closure->returnType' => $stripColon,
            'Expr_Exit->expr' => $stripBoth,
            'Expr_Ternary->if' => $stripBoth,
            'Expr_Yield->key' => $stripDoubleArrow,
            'Expr_Yield->value' => $stripBoth,
            'Param->type' => $stripRight,
            'Param->default' => $stripEquals,
            'Stmt_Break->num' => $stripBoth,
            'Stmt_ClassMethod->returnType' => $stripColon,
            'Stmt_Class->extends' => ['left' => T_EXTENDS],
            'Stmt_Continue->num' => $stripBoth,
            'Stmt_Foreach->keyVar' => $stripDoubleArrow,
            'Stmt_Function->returnType' => $stripColon,
            'Stmt_If->else' => $stripLeft,
            'Stmt_Namespace->name' => $stripLeft,
            'Stmt_PropertyProperty->default' => $stripEquals,
            'Stmt_Return->expr' => $stripBoth,
            'Stmt_StaticVar->default' => $stripEquals,
            'Stmt_TraitUseAdaptation_Alias->newName' => $stripLeft,
            'Stmt_TryCatch->finally' => $stripLeft,
            // 'Stmt_Case->cond': Replace with "default"
            // 'Stmt_Class->name': Unclear what to do
            // 'Stmt_Declare->stmts': Not a plain node
            // 'Stmt_TraitUseAdaptation_Alias->newModifier': Not a plain node
        ];
    }

    protected function initializeInsertionMap() {
        if ($this->insertionMap) return;

        // TODO: "yield" where both key and value are inserted doesn't work
        $this->insertionMap = [
            'Expr_ArrayDimFetch->dim' => ['[', null, null],
            'Expr_ArrayItem->key' => [null, null, ' => '],
            'Expr_Closure->returnType' => [')', ' : ', null],
            'Expr_Ternary->if' => ['?', ' ', ' '],
            'Expr_Yield->key' => [T_YIELD, null, ' => '],
            'Expr_Yield->value' => [T_YIELD, ' ', null],
            'Param->type' => [null, null, ' '],
            'Param->default' => [null, ' = ', null],
            'Stmt_Break->num' => [T_BREAK, ' ', null],
            'Stmt_ClassMethod->returnType' => [')', ' : ', null],
            'Stmt_Class->extends' => [null, ' extends ', null],
            'Stmt_Continue->num' => [T_CONTINUE, ' ', null],
            'Stmt_Foreach->keyVar' => [T_AS, null, ' => '],
            'Stmt_Function->returnType' => [')', ' : ', null],
            //'Stmt_If->else' => [null, ' ', null], // TODO
            'Stmt_Namespace->name' => [T_NAMESPACE, ' ', null],
            'Stmt_PropertyProperty->default' => [null, ' = ', null],
            'Stmt_Return->expr' => [T_RETURN, ' ', null],
            'Stmt_StaticVar->default' => [null, ' = ', null],
            //'Stmt_TraitUseAdaptation_Alias->newName' => [T_AS, ' ', null], // TODO
            'Stmt_TryCatch->finally' => [null, ' ', null],

            // 'Expr_Exit->expr': Complicated due to optional ()
            // 'Stmt_Case->cond': Conversion from default to case
            // 'Stmt_Class->name': Unclear
            // 'Stmt_Declare->stmts': Not a proper node
            // 'Stmt_TraitUseAdaptation_Alias->newModifier': Not a proper node
        ];
    }
}
