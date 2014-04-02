<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property null|Node\Expr $cond  Condition (null for default)
 * @property Node[]         $stmts Statements
 */
class Case_ extends Node\Stmt
{
    /**
     * Constructs a case node.
     *
     * @param null|Node\Expr $cond       Condition (null for default)
     * @param Node[]         $stmts      Statements
     * @param array          $attributes Additional attributes
     */
    public function __construct($cond, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}