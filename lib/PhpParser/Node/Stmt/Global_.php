<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Global_ extends Node\Stmt
{
    /** @var Node\Expr[] Variables */
    public $vars;

    /**
     * Constructs a global variables list node.
     *
     * @param Node\Expr[] $vars       Variables to unset
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->vars = $vars;
    }

    public function getSubNodeNames() {
        return array('vars');
    }
}
