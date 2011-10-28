<?php

/**
 * @property PHPParser_Node[] $stmts Statements
 */
class PHPParser_Node_Stmt_Else extends PHPParser_Node_Stmt
{
    /**
     * Constructs an else node.
     *
     * @param PHPParser_Node[] $stmts      Statements
     * @param int              $line       Line
     * @param null|string      $docComment Nearest doc comment
     */
    public function __construct(array $stmts = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'stmts' => $stmts,
            ),
            $line, $docComment
        );
    }
}