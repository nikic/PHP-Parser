<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Finally_ extends Node\Stmt
{
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a finally node.
     *
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $stmts = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->stmts = $stmts;
    }

    public function getSubNodeNames() : array {
        return array('stmts');
    }
}
