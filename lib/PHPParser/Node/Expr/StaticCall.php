<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 * @property string|PHPParser_Node_Expr              $name  Method name
 * @property PHPParser_Node_Arg[]                    $args  Arguments
 */
class PHPParser_Node_Expr_StaticCall extends PHPParser_Node_Expr
{
    /**
     * Constructs a static method call node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param string|PHPParser_Node_Expr              $name       Method name
     * @param PHPParser_Node_Arg[]                    $args       Arguments
     * @param int                                     $line       Line
     * @param null|string                             $docComment Nearest doc comment
     */
    public function __construct($class, $name, array $args = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'class' => $class,
                'name'  => $name,
                'args'  => $args
            ),
            $line, $docComment
        );
    }
}