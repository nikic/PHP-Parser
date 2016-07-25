<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class NullableType extends NodeAbstract
{
    /** @var string|Name Type */
    public $type;

    /**
     * Constructs a nullable type (wrapping another type).
     *
     * @param string|Name $flags       Type
     * @param array       $attributes Additional attributes
     */
    public function __construct($flags, array $attributes = array()) {
        parent::__construct($attributes);
        $this->type = $flags;
    }

    public function getSubNodeNames() {
        return array('type');
    }
}
