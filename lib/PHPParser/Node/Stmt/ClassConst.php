<?php

/**
 * @property PHPParser_Node_Const[] $consts Constant declarations
 */
class PHPParser_Node_Stmt_ClassConst extends PHPParser_Node_Stmt
{
    /**
     * Constructs a class const list node.
     *
     * @param PHPParser_Node_Const[] $consts     Constant declarations
     * @param int                    $line       Line
     * @param null|string            $docComment Nearest doc comment
     */
    public function __construct(array $consts, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'consts' => $consts,
            ),
            $line, $docComment
        );
    }
}