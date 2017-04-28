<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class NullableType extends NodeAbstract
{
    /** @var Identifier|Name Type */
    public $type;

    /**
     * Constructs a nullable type (wrapping another type).
     *
     * @param string|Identifier|Name $type       Type
     * @param array                  $attributes Additional attributes
     */
    public function __construct($type, array $attributes = array()) {
        parent::__construct($attributes);
        $this->type = \is_string($type) ? new Identifier($type) : $type;
    }

    public function getSubNodeNames() : array {
        return array('type');
    }
}
