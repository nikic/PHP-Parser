<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Expr $cond  Condition
 * @property Case_[]   $cases Case list
 */
class Switch_ extends Node\Stmt
{
    /**
     * Constructs a case node.
     *
     * @param Node\Expr $cond       Condition
     * @param Case_[]   $cases      Case list
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $cases, array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'cases' => $cases,
            ),
            $attributes
        );
    }
}