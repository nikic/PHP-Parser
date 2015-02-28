<?php

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class Encapsed extends Scalar
{
    /** @var array Encaps list */
    public $parts;

    /**
     * Constructs an encapsed string node.
     *
     * @param array $parts      Encaps list
     * @param array $attributes Additional attributes
     */
    public function __construct(array $parts = array(), array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->parts = $parts;
    }

    public function getSubNodeNames() {
        return array('parts');
    }
}
