<?php

/**
 * @property double $value Number value
 */
class PHPParser_Node_Scalar_DNumber extends PHPParser_Node_Scalar
{
    /**
     * Constructs a float number scalar node.
     *
     * @param double      $value      Value of the number
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($value = 0.0, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $line, $docComment
        );
    }
}