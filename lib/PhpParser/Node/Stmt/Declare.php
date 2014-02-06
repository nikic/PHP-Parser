<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property DeclareDeclare[] $declares List of declares
 * @property Node[]           $stmts    Statements
 */
class Declare_ extends Node\Stmt
{
    /**
     * Constructs a declare node.
     *
     * @param DeclareDeclare[] $declares   List of declares
     * @param Node[]           $stmts      Statements
     * @param array            $attributes Additional attributes
     */
    public function __construct(array $declares, array $stmts, array $attributes = array()) {
        parent::__construct(
            array(
                'declares' => $declares,
                'stmts'    => $stmts,
            ),
            $attributes
        );
    }
}