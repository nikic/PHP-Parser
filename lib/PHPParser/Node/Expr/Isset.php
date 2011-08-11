<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables
 */
class PHPParser_Node_Expr_Isset extends PHPParser_Node_Expr
{
    /**
     * Constructs an array node.
     *
     * @param PHPParser_Node_Expr[] $vars       Variables
     * @param int                   $line       Line
     * @param null|string           $docComment Nearest doc comment
     */
    public function __construct(array $vars, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'vars' => $vars
            ),
            $line, $docComment
        );
    }
}