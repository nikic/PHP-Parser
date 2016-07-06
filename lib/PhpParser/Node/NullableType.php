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
     * @param string|Name $type       Type
     * @param array       $attributes Additional attributes
     */
    public function __construct($type, array $attributes = array()) {
        parent::__construct($attributes);
        $this->type = $type;
    }

    public function getSubNodeNames() {
        return array('type');
    }
}
