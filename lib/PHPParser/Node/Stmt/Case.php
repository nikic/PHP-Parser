<?php

/**
 * @property PHPParser_Node[]         $stmts Statements
 * @property null|PHPParser_Node_Expr $cond Condition (null for default)
 */
class PHPParser_Node_Stmt_Case extends PHPParser_Node_Stmt
{
    /**
     * Constructs a case node.
     *
     * @param PHPParser_Node[]         $stmts      Statements
     * @param null|PHPParser_Node_Expr $cond       Condition (null for default)
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct(array $stmts, PHPParser_Node_Expr $cond = null, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'stmts' => $stmts,
                'cond'  => $cond,
            ),
            $line, $docComment
        );
    }
}