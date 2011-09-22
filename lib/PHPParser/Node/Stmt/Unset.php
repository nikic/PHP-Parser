<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables to unset
 */
class PHPParser_Node_Stmt_Unset extends PHPParser_Node_Stmt
{
    /**
     * Constructs an unset node.
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