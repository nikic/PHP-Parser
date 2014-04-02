<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Expr $cond  Condition
 * @property Node[]    $stmts Statements
 */
class ElseIf_ extends Node\Stmt
{
    /**
     * Constructs an elseif node.
     *
     * @param Node\Expr $cond       Condition
     * @param Node[]    $stmts      Statements
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}