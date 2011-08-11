<?php

/**
 * @property array               $assignList List of variables to assign to
 * @property PHPParser_Node_Expr $expr       Expression
 */
class PHPParser_Node_Expr_List extends PHPParser_Node_Expr
{
    /**
     * Constructs a list() assignment node.
     *
     * @param array               $assignList List of variables to assign to
     * @param PHPParser_Node_Expr $expr       Expression
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(array $assignList, PHPParser_Node_Expr $expr, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'assignList' => $assignList,
                'expr'       => $expr
            ),
            $line, $docComment
        );
    }
}