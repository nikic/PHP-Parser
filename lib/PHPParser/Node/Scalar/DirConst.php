<?php

class PHPParser_Node_Scalar_DirConst extends PHPParser_Node_Scalar
{
    /**
     * Constructs a __DIR__ const node
     *
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($line = -1, $docComment = null) {
        parent::__construct(array(), $line, $docComment);
    }
}