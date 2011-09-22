<?php

/**
 * @property PHPParser_Node_Expr        $var  Variable holding object
 * @property string|PHPParser_Node_Expr $name Method name
 * @property PHPParser_Node_Arg[]       $args Arguments
 */
class PHPParser_Node_Expr_MethodCall extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Expr        $var        Variable holding object
     * @param string|PHPParser_Node_Expr $name       Method name
     * @param PHPParser_Node_Arg[]       $args       Arguments
     * @param int                        $line       Line
     * @param null|string                $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $var, $name, array $args = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'var'  => $var,
                'name' => $name,
                'args' => $args
            ),
            $line, $docComment
        );
    }
}