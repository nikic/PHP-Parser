<?php

/**
 * @property PHPParser_Node_Stmts_StaticVar[] $vars Variable definitions
 */
class PHPParser_Node_Stmt_Static extends PHPParser_Node_Stmt
{
    /**
     * Constructs a static variables list node.
     *
     * @param PHPParser_Node_Stmts_StaticVar[] $vars       Variable definitions
     * @param int                              $line       Line
     * @param null|string                      $docComment Nearest doc comment
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