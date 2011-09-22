<?php

/**
 * @property string $name Name
 */
class PHPParser_Node_Stmt_Label extends PHPParser_Node_Stmt
{
    /**
     * Constructs a label node.
     *
     * @param string      $name       Name
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