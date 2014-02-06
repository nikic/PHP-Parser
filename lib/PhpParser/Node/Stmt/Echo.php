<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Expr[] $exprs Expressions
 */
class Echo_ extends Node\Stmt
{
    /**
     * Constructs an echo node.
     *
     * @param Node\Expr[] $exprs      Expressions
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $exprs, array $attributes = array()) {
        parent::__construct(
            array(
                'exprs' => $exprs,
            ),
            $attributes
        );
    }
}