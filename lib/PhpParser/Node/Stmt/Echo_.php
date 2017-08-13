<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Echo_ extends Node\Stmt
{
    /** @var Node\Expr[] Expressions */
    public $exprs;

    /**
     * Constructs an echo node.
     *
     * @param Node\Expr[] $exprs      Expressions
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $exprs, array $attributes = []) {
        parent::__construct($attributes);
        $this->exprs = $exprs;
    }

    public function getSubNodeNames() : array {
        return ['exprs'];
    }
}
