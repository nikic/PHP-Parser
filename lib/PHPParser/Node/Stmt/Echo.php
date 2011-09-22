<?php

/**
 * @property PHPParser_Node_Expr[] $exprs Expressions
 */
class PHPParser_Node_Stmt_Echo extends PHPParser_Node_Stmt
{
    /**
     * Constructs an echo node.
     *
     * @param PHPParser_Node_Expr[] $exprs      Expressions
     * @param int                   $line       Line
     * @param null|string           $docComment Nearest doc comment
     */
    public function __construct(array $exprs, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'exprs' => $exprs,
            ),
            $line, $docComment
        );
    }
}