<?php

/**
 * @property int $value Number value
 */
class PHPParser_Node_Scalar_LNumber extends PHPParser_Node_Scalar
{
    /**
     * Constructs an integer number scalar node.
     *
     * @param int         $value      Value of the number
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($value = 0, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $line, $docComment
        );
    }
}