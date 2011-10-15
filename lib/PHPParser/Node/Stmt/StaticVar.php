<?php

/**
 * @property string                   $name    Name
 * @property null|PHPParser_Node_Expr $default Default value
 */
class PHPParser_Node_Stmt_StaticVar extends PHPParser_Node_Stmt
{
    /**
     * Constructs a static variable node.
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