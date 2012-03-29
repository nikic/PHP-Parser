<?php

/**
 * @property PHPParser_Node_Expr $expr The expression wrapped in this statement.
 */
class PHPParser_Node_Stmt_Expr extends PHPParser_Node_Stmt
{
    /**
     * Constructs an expr node.
     *
     * @param PHPParser_Node_Expr      $expr       Expr wrapped in this statement
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $expr, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'expr' => $expr,
            ),
            $line, $docComment
        );
    }
}