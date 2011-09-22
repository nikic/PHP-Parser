<?php

/**
 * @property string $value String
 */
class PHPParser_Node_Stmt_InlineHTML extends PHPParser_Node_Stmt
{
    /**
     * Constructs an inline HTML node.
     *
     * @param string      $value      String
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($value, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'value' => $value,
            ),
            $line, $docComment
        );
    }
}