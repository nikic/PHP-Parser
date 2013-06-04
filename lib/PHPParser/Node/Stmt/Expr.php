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
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $expr, array $attributes = array()) {
        parent::__construct(
            array(
                'expr' => $expr,
            ),
            $attributes
        );
    }
}