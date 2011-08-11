<?php

/**
 * @property PHPParser_Node_Expr $value Value to pass
 * @property bool                $byRef Whether to pass by ref
 */
class PHPParser_Node_Expr_FuncCallArg extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call argument node.
     *
     * @param PHPParser_Node_Expr $value      Value to pass
     * @param bool                $byRef      Whether to pass by ref
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $value, $byRef = false, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'value' => $value,
                'byRef' => $byRef
            ),
            $line, $docComment
        );
    }
}