<?php

/**
 * @property PHPParser_Node_Expr      $cond Condition
 * @property null|PHPParser_Node_Expr $if   Expression for true
 * @property PHPParser_Node_Expr      $else Expression for false
 */
class PHPParser_Node_Expr_Ternary extends PHPParser_Node_Expr
{
    /**
     * Constructs a ternary operator node.
     *
     * @param PHPParser_Node_Expr      $cond       Condition
     * @param null|PHPParser_Node_Expr $if         Expression for true
     * @param PHPParser_Node_Expr      $else       Expression for false
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $cond, $if, PHPParser_Node_Expr $else, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'cond' => $cond,
                'if'   => $if,
                'else' => $else
            ),
            $line, $docComment
        );
    }
}