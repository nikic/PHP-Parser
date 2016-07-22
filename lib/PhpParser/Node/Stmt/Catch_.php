<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Catch_ extends Node\Stmt
{
    /** @var Node\Name[] Types of exceptions to catch */
    public $types;
    /** @var string Variable for exception */
    public $var;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a catch node.
     *
     * @param Node\Name[] $types      Types of exceptions to catch
     * @param string      $var        Variable for exception
     * @param Node[]      $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $types, $var, array $stmts = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->types = $types;
        $this->var = $var;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames() {
        return array('types', 'var', 'stmts');
    }
}
