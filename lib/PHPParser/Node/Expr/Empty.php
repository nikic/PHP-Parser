<?php

/**
 * @property PHPParser_Node_Expr $var Variable
 */
class PHPParser_Node_Expr_Empty extends PHPParser_Node_Expr
{
    /**
     * Constructs an empty() node.
     *
     * @param PHPParser_Node_Expr $var        Variable
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $var, array $attributes = array()) {
        parent::__construct(
            array(
                'var' => $var
            ),
            $attributes
        );
    }
}