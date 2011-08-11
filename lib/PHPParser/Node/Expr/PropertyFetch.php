<?php

/**
 * @property PHPParser_Node_Expr        $var  Variable holding object
 * @property string|PHPParser_Node_Expr $name Property Name
 */
class PHPParser_Node_Expr_PropertyFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Expr        $var        Variable holding object
     * @param string|PHPParser_Node_Expr $name       Property name
     * @param int                        $line       Line
     * @param null|string                $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $var, $name, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'var'  => $var,
                'name' => $name
            ),
            $line, $docComment
        );
    }
}