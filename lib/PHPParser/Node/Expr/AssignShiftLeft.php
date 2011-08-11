<?php

/**
 * @property PHPParser_Node_Expr $var  Variable
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_AssignShiftLeft extends PHPParser_Node_Expr
{
    /**
     * Constructs an assignment with left shift node.
     *
     * @param PHPParser_Node_Expr $var        Variable
     * @param PHPParser_Node_Expr $expr       Expression
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $var, PHPParser_Node_Expr $expr, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'var'  => $var,
                'expr' => $expr
            ),
            $line, $docComment
        );
    }
}