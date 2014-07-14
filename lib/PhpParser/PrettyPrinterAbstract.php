<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class PrettyPrinterAbstract
{
    protected $precedenceMap = array(
        // [precedence, associativity] where for the latter -1 is %left, 0 is %nonassoc and 1 is %right
        'Expr_BinaryOp_Pow'            => array( 0,  1),
        'Expr_BitwiseNot'              => array( 1,  1),
        'Expr_PreInc'                  => array( 1,  1),
        'Expr_PreDec'                  => array( 1,  1),
        'Expr_PostInc'                 => array( 1, -1),
        'Expr_PostDec'                 => array( 1, -1),
        'Expr_UnaryPlus'               => array( 1,  1),
        'Expr_UnaryMinus'              => array( 1,  1),
        'Expr_Cast_Int'                => array( 1,  1),
        'Expr_Cast_Double'             => array( 1,  1),
        'Expr_Cast_String'             => array( 1,  1),
        'Expr_Cast_Array'              => array( 1,  1),
        'Expr_Cast_Object'             => array( 1,  1),
        'Expr_Cast_Bool'               => array( 1,  1),
        'Expr_Cast_Unset'              => array( 1,  1),
        'Expr_ErrorSuppress'           => array( 1,  1),
        'Expr_Instanceof'              => array( 2,  0),
        'Expr_BooleanNot'              => array( 3,  1),
        'Expr_BinaryOp_Mul'            => array( 4, -1),
        'Expr_BinaryOp_Div'            => array( 4, -1),
        'Expr_BinaryOp_Mod'            => array( 4, -1),
        'Expr_BinaryOp_Plus'           => array( 5, -1),
        'Expr_BinaryOp_Minus'          => array( 5, -1),
        'Expr_BinaryOp_Concat'         => array( 5, -1),
        'Expr_BinaryOp_ShiftLeft'      => array( 6, -1),
        'Expr_BinaryOp_ShiftRight'     => array( 6, -1),
        'Expr_BinaryOp_Smaller'        => array( 7,  0),
        'Expr_BinaryOp_SmallerOrEqual' => array( 7,  0),
        'Expr_BinaryOp_Greater'        => array( 7,  0),
        'Expr_BinaryOp_GreaterOrEqual' => array( 7,  0),
        'Expr_BinaryOp_Equal'          => array( 8,  0),
        'Expr_BinaryOp_NotEqual'       => array( 8,  0),
        'Expr_BinaryOp_Identical'      => array( 8,  0),
        'Expr_BinaryOp_NotIdentical'   => array( 8,  0),
        'Expr_BinaryOp_BitwiseAnd'     => array( 9, -1),
        'Expr_BinaryOp_BitwiseXor'     => array(10, -1),
        'Expr_BinaryOp_BitwiseOr'      => array(11, -1),
        'Expr_BinaryOp_BooleanAnd'     => array(12, -1),
        'Expr_BinaryOp_BooleanOr'      => array(13, -1),
        'Expr_Ternary'                 => array(14, -1),
        // parser uses %left for assignments, but they really behave as %right
        'Expr_Assign'                  => array(15,  1),
        'Expr_AssignRef'               => array(15,  1),
        'Expr_AssignOp_Plus'           => array(15,  1),
        'Expr_AssignOp_Minus'          => array(15,  1),
        'Expr_AssignOp_Mul'            => array(15,  1),
        'Expr_AssignOp_Div'            => array(15,  1),
        'Expr_AssignOp_Concat'         => array(15,  1),
        'Expr_AssignOp_Mod'            => array(15,  1),
        'Expr_AssignOp_BitwiseAnd'     => array(15,  1),
        'Expr_AssignOp_BitwiseOr'      => array(15,  1),
        'Expr_AssignOp_BitwiseXor'     => array(15,  1),
        'Expr_AssignOp_ShiftLeft'      => array(15,  1),
        'Expr_AssignOp_ShiftRight'     => array(15,  1),
        'Expr_AssignOp_Pow'            => array(15,  1),
        'Expr_BinaryOp_LogicalAnd'     => array(16, -1),
        'Expr_BinaryOp_LogicalXor'     => array(17, -1),
        'Expr_BinaryOp_LogicalOr'      => array(18, -1),
        'Expr_Include'                 => array(19, -1),
    );

    protected $noIndentToken;
    protected $canUseSemicolonNamespaces;

    // Indent before any close brace (all are preceded by newline)
    protected $indentClose = false;

    // Indent before any open brace that is preceded by a newline
    protected $indentOpen = false;

    // String used for indentation
    protected $indentString = '    ';

    // Set to non-zero to wrap before that line length if possible
    protected $lineWrap = 0;

    // Callback function to use on predefined names, that is, true, false, and null
    protected $nameCallback = null;

    // Number of spaces a tab is used for to determine line length
    protected $tabStop = 8;

    // Preserve heredoc and nowdoc strings where they are found in source input.
    protected $preserveHeredoc = false;

    // Use indent within arrays
    protected $useArrayIndent = false;

    // Add a blank line before any comment
    protected $useBlankBeforeComment = false;

    // Should a newline be used before the brace?
    protected $braceNewlines = array(
        'catch' => false,
        'class' => false,
        'closure' => false,
        'declare' => false,
        'do' => false,
        'else' => false,
        'elseif' => false,
        'finally' => false,
        'foreach' => false,
        'for' => false,
        'function' => false,
        'if' => false,
        'interface' => false,
        'method' => false,
        'namespace' => false,
        'switch' => false,
        'trait' => false,
        'try' => false,
        'use' => false,
        'while' => false,
    );

    // Should a newline be used before the keyword?
    protected $keywordNewlines = array(
        'catch' => false,
        'else' => false,
        'elseif' => false,
        'finally' => false,
        'while' => false,
    );

    // Tokens that can be wrapped at
    protected $wrapTokens = array();

    public function __construct() {
        $this->noIndentToken = '_NO_INDENT_' . mt_rand();
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts) {
        $this->preprocessNodes($stmts);

        return ltrim(str_replace("\n" . $this->noIndentToken, "\n", $this->pStmts($stmts, false)));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node) {
        return str_replace("\n" . $this->noIndentToken, "\n", $this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     * @param bool $useCloseTag Whether to use a closing tag
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts, $useCloseTag = false) {
        $p = rtrim($this->prettyPrint($stmts));

        // Remove the initial closing tag if it is empty
        $p = preg_replace('/^\?>\n?/', '', $p, -1, $closeCount);

        // Remove the final opening tag if it is empty
        $p = preg_replace('/<\?php$/', '', $p, -1, $openCount);

        // The initial closing tag was not empty, so add an initial open tag for it
        if (!$closeCount) {
            $p = "<?php\n" . $p;
        }

        // The final opening tag was not empty, so add a final closing tag for it, if requested
        if (!$openCount) {
             // The newline is necessary even without the closing tag in case file ends with a heredoc
             $p .= "\n";
             if ($useCloseTag) {
                 $p .= "?>";
             }
        }
        
        return $p;
    }

    /**
     * Use indent before any close brace (all are preceded by newline).
     *
     * @param bool $indentClose
     */
    public function setIndentClose($indentClose) {
        $this->indentClose = $indentClose;
    }

    /**
     * Use indent before any open brace that is preceded by a newline.
     *
     * @param bool $indentOpen
     */
    public function setIndentOpen($indentOpen) {
        $this->indentOpen = $indentOpen;
    }

    /**
     * Set the string of indent characters to use.
     *
     * @param string $indentString
     */
    public function setIndentString($indentString) {
        $this->indentString = $indentString;
    }

    /**
     * Set the value of the line wrap
     *
     * @param int $lineWrap
     */
    public function setLineWrap($lineWrap) {
        $this->lineWrap = $lineWrap;
    }

    /**
     * Use predefined name callback function.
     *
     * @param callable $nameCallback
     */
    public function setNameCallback($nameCallback) {
        $this->nameCallback = $nameCallback;
    }

    /**
     * Set to true to preserve heredoc and nowdoc strings.
     *
     * @param bool $preserveHeredoc
     */
    public function setPreserveHeredoc($preserveHeredoc) {
        $this->preserveHeredoc = $preserveHeredoc;
    }

    /**
     * Set the value of tab stop.
     *
     * @param int $tabStop
     */
    public function setTabStop($tabStop) {
        $this->tabStop = $tabStop;
    }

    /**
     * Use indentation inside arrays.
     *
     * @param bool $useArrayIndent
     */
    public function setUseArrayIndent($useArrayIndent) {
        $this->useArrayIndent = $useArrayIndent;
    }

    /**
     * Use a blank line before any comment.
     *
     * @param bool $useBlankBeforeComment
     */
    public function setUseBlankBeforeComment($useBlankBeforeComment) {
        $this->useBlankBeforeComment = $useBlankBeforeComment;
    }

    /**
     * Set the value of a brace newline parameter
     *
     * @param string $keyword
     * @param bool $value
     */
    public function setBraceNewline($keyword, $value = true) {
        $this->braceNewlines[$keyword] = $value;
    }

    /**
     * Set the value of a keyword newline parameter
     *
     * @param string $keyword
     * @param bool $value
     */
    public function setKeywordNewline($keyword, $value = true) {
        $this->keywordNewlines[$keyword] = $value;
    }

    /**
     * Add another PHP token to wrap at in case line length exceeds total
     *
     * @param int $tokenType
     */
    public function addWrapToken($tokenType) {
        $this->wrapTokens[$tokenType] = true;
    }

    /**
     * Wrap source code using PHP tokenizer.
     *
     * @param string $sourceCode
     *
     * @return string
     */
    public function wrapLines($sourceCode) {
        if ($this->lineWrap == 0 || ! $this->wrapTokens) {
            return $sourceCode;
        }
        $tokens = token_get_all($sourceCode);

        // Split into lines and identify wrappable tokens
        $lines = array();
        $line = array();
        $part = array();
        foreach ($tokens as $i => $token) {
            if (is_array($token)) {
                list($type, $text) = $token;
                // Remove extra space from PHP open tag if followed by newline
                if ($type == T_OPEN_TAG && isset($tokens[$i + 1]) && is_array($tokens[$i + 1]) &&
                    ctype_space(substr($tokens[$i + 1][1], 0, 1))) {
                    $text = rtrim($text);
                }
                // Replace whitespace character codes with characters in long strings
                if ($type == T_ENCAPSED_AND_WHITESPACE && !$this->shouldEncodeWhitespace($text, true)) {
                    $from = array('\\n', '\\r', '\\t', '\\f', '\\v');
                    $to = array("\n", "\r", "\t", "\f", "\v");
                    $text = str_replace($from, $to, $text);
                }
            }
            else {
                $type = $text = $token;
            }
            $frags = preg_split("/(\n)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            foreach ($frags as $frag) {
                if ($frag == "\n") {
                    // Newlines end a line, so add any existing part then reset line and part.
                    if ($part) {
                        $line[] = $part;
                        $part = array();
                    }
                    $line[] = array(
                        "\n",
                        false
                    );
                    $lines[] = $line;
                    $line = array();
                }
                elseif (isset($this->wrapTokens[$type])) {
                    // Wrappable parts are stored separately within a line, so add any existing part then add wrap.
                    if ($part) {
                        $line[] = $part;
                        $part = array();
                    }
                    $line[] = array(
                        $frag,
                        true
                    );
                }
                else {
                    // Non-wrappable parts accumulate until they are added to a line
                    if ($part) {
                        $part[0] .= $frag;
                    }
                    else {
                        $part = array(
                            $frag,
                            false
                        );
                    }
                }
            }
        }

        // Add any leftovers.
        if ($part) {
            $line[] = $part;
        }
        if ($line) {
            $lines[] = $line;
        }

        // Prepare output.
        $prevWrappable = false;
        $output = '';
        foreach ($lines as $line) {
            preg_match('/^[ \t]*/', $line[0][0], $match);
            $indent = $match[0];
            $lineLen = 0;
            foreach ($line as $part) {
                list($frag, $wrappable) = $part;
                $fragLen = $this->lineLen($frag);
                if ($prevWrappable
                        && $fragLen > 1
                        && $lineLen + $fragLen >= $this->lineWrap) {
                    $wrapFrag = ltrim($frag);
                    $wrapFragLen = $this->lineLen($wrapFrag);
                    $start = $indent . $this->indentString;
                    $lineLen = $this->lineLen($start) + $wrapFragLen;
                    $output .= "\n" . $start . $wrapFrag;
                } else {
                    $lineLen += $fragLen;
                    $output .= $frag;
                }
                $prevWrappable = $wrappable;
            }
        }
        return $output;
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
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param Node[] $nodes  Array of nodes
     * @param bool   $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true) {
        $result = '';
        foreach ($nodes as $node) {
            $comments = $node->getAttribute('comments', array());
            if ($comments && $this->useBlankBeforeComment) {
                $result .= "\n" . $this->noIndentToken;
            }
            $result .= "\n"
                    . $this->pComments($comments)
                    . $this->p($node)
                    . ($node instanceof Expr ? ';' : '');
        }

        if ($indent) {
            return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n" . $this->indentString, $result);
        } else {
            return $result;
        }
    }

    /**
     * Pretty prints a node.
     *
     * @param Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(Node $node) {
        return $this->{'p' . $node->getType()}($node);
    }

    protected function pInfixOp($type, Node $leftNode, $operatorString, Node $rightNode) {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
             . $operatorString
             . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }

    protected function pPrefixOp($type, $operatorString, Node $node) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }

    protected function pPostfixOp($type, Node $node, $operatorString) {
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
    protected function pPrec(Node $node, $parentPrecedence, $parentAssociativity, $childPosition) {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->{'p' . $type}($node) . ')';
            }
        }

        return $this->{'p' . $type}($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param string $glue  Character to implode with
     *
     * @return string Imploded pretty printed nodes
     */
    protected function pImplode(array $nodes, $glue = '') {
        $pNodes = array();
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
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
    protected function pCommaSeparated(array $nodes) {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Signals the pretty printer that a string shall not be indented.
     *
     * @param string $string Not to be indented string
     *
     * @return mixed String marked with $this->noIndentToken's.
     */
    protected function pNoIndent($string) {
        return str_replace("\n", "\n" . $this->noIndentToken, $string);
    }

    protected function pComments(array $comments) {
        $result = '';

        foreach ($comments as $comment) {
            $result .= $comment->getReformattedText() . "\n";
        }

        return $result;
    }

    /**
     * Get a close brace.
     *
     * @return string
     */
    protected function closeBrace() {
        if ($this->indentClose) {
            $brace = "\n" . $this->indentString . '}';
        } else {
            $brace = "\n" . '}';
        }
        return $brace;
    }

    /**
     * Get an open brace of a particular kind.
     *
     * @param string $keyword
     * 
     * @return string
     */
    protected function openBrace($keyword) {
        $useNewline = isset($this->braceNewlines[$keyword]) ? $this->braceNewlines[$keyword] : false;
        if ($useNewline) {
            if ($this->indentOpen) {
                $brace = "\n" . $this->indentString . '{';
            } else {
                $brace = "\n" . '{';
            }
        } else {
            $brace = ' {';
        }
        return $brace;
    }

    /**
     * Get a keyword, which may be preceded by a newline.
     */
    protected function keyword($keyword) {
        $useNewline = isset($this->keywordNewlines[$keyword]) ? $this->keywordNewlines[$keyword] : false;
        if ($useNewline) {
            $keyword = "\n" . $keyword;
        } else {
            $keyword = ' ' . $keyword;
        }
        return $keyword;
    }

    /**
     * Get the length of a line, including expansion of tabs.
     *
     * @param string $line
     *
     * @return int
     */
    protected function lineLen($line) {
        // Subtract 1 from $tabStop because the tab itself is already included in the length.
        return strlen($line) + substr_count($line, "\t") * ($this->tabStop - 1);
    }

    /**
     * Should we encode the whitespace in a string?
     *
     * The default method is to allow whitespace encoded characters only if newlines appear at the end of the string.
     *
     * @param string $string
     * @param bool $alreadyEncoded Has the string we are checking already been encoded?
     *
     * @return bool
     */
    protected function shouldEncodeWhitespace($string, $alreadyEncoded = false) {
        if ($alreadyEncoded) {
            $string = str_replace('\\n', "\n", $string);
        }
        return preg_match('/^[^\\n]*[\\n]+$/', $string);
    }
}
