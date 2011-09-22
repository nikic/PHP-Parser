<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables
 */
class PHPParser_Node_Stmt_Global extends PHPParser_Node_Stmt
{
    /**
     * Constructs a global variables list node.
     *
     * @param PHPParser_Node_Expr[] $vars       Variables to unset
     * @param int                   $line       Line
     * @param null|string           $docComment Nearest doc comment
     */
    public function __construct(array $vars, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'vars' => $vars,
            ),
            $line, $docComment
        );
    }
}