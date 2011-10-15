<?php

/**
 * @property int                                    $type  Modifiers
 * @property PHPParser_Node_Stmt_PropertyProperty[] $props Properties
 */
class PHPParser_Node_Stmt_Property extends PHPParser_Node_Stmt
{
    /**
     * Constructs a class property list node.
     *
     * @param int                                    $type       Modifiers
     * @param PHPParser_Node_Stmt_PropertyProperty[] $props      Properties
     * @param int                                    $line       Line
     * @param null|string                            $docComment Nearest doc comment
     */
    public function __construct($type, array $props, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'type'  => $type,
                'props' => $props,
            ),
            $line, $docComment
        );
    }
}