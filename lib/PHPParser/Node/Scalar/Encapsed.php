<?php

/**
 * @property array $parts Encaps list
 */
class PHPParser_Node_Scalar_Encapsed extends PHPParser_Node_Scalar
{
    /**
     * Constructs an encapsed string node.
     *
     * @param array       $parts      Encaps list
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct(array $parts = array(), $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'parts' => $parts
            ),
            $line, $docComment
        );
    }
}