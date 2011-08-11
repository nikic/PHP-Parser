<?php

/**
 * @property array $parts Encapsed string array
 */
class PHPParser_Node_Expr_ShellExec extends PHPParser_Node_Expr
{
    /**
     * Constructs a shell exec (backtick) node.
     *
     * @param array       $parts      Encapsed string array
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($parts, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'parts' => $parts
            ),
            $line, $docComment
        );
    }
}