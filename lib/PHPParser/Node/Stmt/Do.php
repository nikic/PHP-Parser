<?php

/**
 * @property PHPParser_Node_Expr $cond  Condition
 * @property PHPParser_Node[]    $stmts Statements
 */
class PHPParser_Node_Stmt_Do extends PHPParser_Node_Stmt
{
    /**
     * Constructs a do while node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param PHPParser_Node[]    $stmts      Statements
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $cond, array $stmts = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $line, $docComment
        );
    }
}