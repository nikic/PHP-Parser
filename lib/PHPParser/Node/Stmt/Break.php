<?php

/**
 * @property null|PHPParser_Node_Expr $num Number of loops to break
 */
class PHPParser_Node_Stmt_Break extends PHPParser_Node_Stmt
{
    /**
     * Constructs a break node.
     *
     * @param null|PHPParser_Node_Expr $num        Number of loops to break
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $num = null, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'num' => $num,
            ),
            $line, $docComment
        );
    }
}