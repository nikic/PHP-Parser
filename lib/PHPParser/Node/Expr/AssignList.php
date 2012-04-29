<?php

/**
 * @property array               $vars List of variables to assign to
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_AssignList extends PHPParser_Node_Expr
{
    /**
     * Constructs a list() assignment node.
     *
     * @param array               $vars       List of variables to assign to
     * @param PHPParser_Node_Expr $expr       Expression
     * @param array               $attributes Additional attributes
     */
    public function __construct(array $vars, PHPParser_Node_Expr $expr, array $attributes = array()) {
        parent::__construct(
            array(
                'vars' => $vars,
                'expr' => $expr
            ),
            $attributes
        );
    }
}