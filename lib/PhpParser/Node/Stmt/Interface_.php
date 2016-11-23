<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Interface_ extends ClassLike
{
    /** @var Node\Name[] Extended interfaces */
    public $extends;

    /**
     * Constructs a class node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'extends' => array(): Name of extended interfaces
     *                           'stmts'   => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
        $this->extends = isset($subNodes['extends']) ? $subNodes['extends'] : array();
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();
    }

    public function getSubNodeNames() {
        return array('name', 'extends', 'stmts');
    }
}
