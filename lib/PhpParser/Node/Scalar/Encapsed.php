<?php

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

/**
 * @property array $parts Encaps list
 */
class Encapsed extends Scalar
{
    /**
     * Constructs an encapsed string node.
     *
     * @param array $parts      Encaps list
     * @param array $attributes Additional attributes
     */
    public function __construct(array $parts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'parts' => $parts
            ),
            $attributes
        );
    }
}