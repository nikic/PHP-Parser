<?php

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;
use PhpParser\Node\Name;

abstract class MagicConst extends Scalar
{
    /**
     * Constructs a magic constant node.
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = array()) {
        $name = explode('\\', get_class($this));
        $name = trim(strtoupper(array_pop($name)), '_');

        parent::__construct(
            array(
                'name' => new Name(sprintf('__%s__', $name))
            ),
            $attributes
        );
    }
}
