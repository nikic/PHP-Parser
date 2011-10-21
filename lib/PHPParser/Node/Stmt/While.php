<?php

/**
 * @property PHPParser_Node_Expr $cond  Condition
 * @property PHPParsre_Node[]    $stmts Statements
 */
class PHPParser_Node_Stmt_While extends PHPParser_Node_Stmt
{
    /**
     * Constructs a while node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param PHPParser_Node[]    $stmts      Statements
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct($cond, array $stmts, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $line, $docComment
        );
    }
}