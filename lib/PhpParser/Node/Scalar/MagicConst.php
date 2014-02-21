<?php

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

abstract class MagicConst extends Scalar
{
    /**
     * Constructs a magic constant node.
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct(array(), $attributes);
    }

    /**
     * Get name of magic constant.
     *
     * @return string Name of magic constant
     */
    abstract public function getName();
}