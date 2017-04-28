<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike
{
    /**
     * Constructs a trait node.
     *
     * @param string|Node\Identifier $name Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'stmts' => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();
    }

    public function getSubNodeNames() : array {
        return array('name', 'stmts');
    }
}
