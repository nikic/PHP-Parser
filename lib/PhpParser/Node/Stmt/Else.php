<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node[] $stmts Statements
 */
class Else_ extends Node\Stmt
{
    /**
     * Constructs an else node.
     *
     * @param Node[] $stmts      Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}