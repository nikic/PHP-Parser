<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Name $type  Class of exception
 * @property string    $var   Variable for exception
 * @property Node[]    $stmts Statements
 */
class Catch_ extends Node\Stmt
{
    /**
     * Constructs a catch node.
     *
     * @param Node\Name $type       Class of exception
     * @param string    $var        Variable for exception
     * @param Node[]    $stmts      Statements
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Name $type, $var, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'type'  => $type,
                'var'   => $var,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}