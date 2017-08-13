<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Else_ extends Node\Stmt
{
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs an else node.
     *
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $stmts = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->stmts = $stmts;
    }

    public function getSubNodeNames() : array {
        return ['stmts'];
    }
}
