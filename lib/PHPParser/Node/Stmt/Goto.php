<?php

/**
 * @property string $name Name of label to jump to
 */
class PHPParser_Node_Stmt_Goto extends PHPParser_Node_Stmt
{
    /**
     * Constructs a goto node.
     *
     * @param string      $name       Name of label to jump to
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($name, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'name' => $name,
            ),
            $line, $docComment
        );
    }
}