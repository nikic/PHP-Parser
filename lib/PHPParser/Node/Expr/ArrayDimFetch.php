<?php

/**
 * @property PHPParser_Node_Expr      $var Variable
 * @property null|PHPParser_Node_Expr $dim Array index / dim
 */
class PHPParser_Node_Expr_ArrayDimFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs an array index fetch node.
     *
     * @param PHPParser_Node_Expr      $var        Variable
     * @param null|PHPParser_Node_Expr $dim        Array index / dim
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $var, PHPParser_Node_Expr $dim = null, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'var' => $var,
                'dim' => $dim
            ),
            $line, $docComment
        );
    }
}