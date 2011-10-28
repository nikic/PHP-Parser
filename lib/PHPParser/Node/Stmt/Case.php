<?php

/**
 * @property null|PHPParser_Node_Expr $cond  Condition (null for default)
 * @property PHPParser_Node[]         $stmts Statements
 */
class PHPParser_Node_Stmt_Case extends PHPParser_Node_Stmt
{
    /**
     * Constructs a case node.
     *
     * @param null|PHPParser_Node_Expr $cond       Condition (null for default)
     * @param PHPParser_Node[]         $stmts      Statements
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct($cond, array $stmts = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'stmts' => $stmts,
                'cond'  => $cond,
            ),
            $line, $docComment
        );
    }
}