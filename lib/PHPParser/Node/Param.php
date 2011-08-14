<?php

/**
 * @property string                          $name    Name
 * @property null|PHPParser_Node_Expr        $default Default value
 * @property null|string|PHPParser_Node_Name $type    Typehint
 * @property bool                            $byRef   Whether is passed by reference
 */
class PHPParser_Node_Param extends PHPParser_NodeAbstract
{
    /**
     * Constructs a parameter node.
     *
     * @param string                          $name       Name
     * @param null|PHPParser_Node_Expr        $default    Default value
     * @param null|string|PHPParser_Node_Name $type       Typehint
     * @param bool                            $byRef      Whether is passed by reference
     * @param int                             $line       Line
     * @param null|string                     $docComment Nearest doc comment
     */
    public function __construct($name, $default = null, $type = null, $byRef = false, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'name'    => $name,
                'default' => $default,
                'type'    => $type,
                'byRef'   => $byRef
            ),
            $line, $docComment
        );
    }
}