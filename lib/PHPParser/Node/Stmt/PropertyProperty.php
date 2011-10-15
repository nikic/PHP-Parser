<?php

/**
 * @property string                   $name    Name
 * @property null|PHPParser_Node_Expr $default Default
 */
class PHPParser_Node_Stmt_PropertyProperty extends PHPParser_Node_Stmt
{
    /**
     * Constructs a class property node.
     *
     * @param string                   $name       Name
     * @param null|PHPParser_Node_Expr $default    Default value
     * @param int                      $line       Line
     * @param null|string              $docComment Nearest doc comment
     */
    public function __construct($name, PHPParser_Node_Expr $default = null, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'name'    => $name,
                'default' => $default,
            ),
            $line, $docComment
        );
    }
}