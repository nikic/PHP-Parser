<?php

/**
 * @property PHPParser_Node_Expr $left  The left hand side expression
 * @property PHPParser_Node_Expr $right The right hand side expression
 */
class PHPParser_Node_Expr_Mul extends PHPParser_Node_Expr
{
    /**
     * Constructs a multiplication node.
     *
     * @param PHPParser_Node_Expr $left       The left hand side expression
     * @param PHPParser_Node_Expr $right      The right hand side expression
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $left, PHPParser_Node_Expr $right, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'left'  => $left,
                'right' => $right
            ),
            $line, $docComment
        );
    }
}