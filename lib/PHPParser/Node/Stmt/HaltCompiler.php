<?php

/**
 * @property string $remaining Remaining text after halt compiler statement.
 */
class PHPParser_Node_Stmt_HaltCompiler extends PHPParser_Node_Stmt
{
    /**
     * Constructs a __halt_compiler node.
     *
     * @param string      $remaining  Remaining text after halt compiler statement.
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($remaining, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'remaining' => $remaining,
            ),
            $line, $docComment
        );
    }
}