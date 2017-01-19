<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * Represents a non-namespaced name. Namespaced names are represented using Name nodes.
 */
class Identifier extends NodeAbstract
{
    /** @var string Identifier as string */
    public $name;

    /**
     * Constructs an identifier node.
     *
     * @param string $name       Identifier as string
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
    }

    public function getSubNodeNames() {
        return array('name');
    }

    /**
     * Get identifier as string.
     *
     * @return string Identifier as string.
     */
    public function __toString() {
        return $this->name;
    }
}
