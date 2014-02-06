<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property string    $key   Key
 * @property Node\Expr $value Value
 */
class DeclareDeclare extends Node\Stmt
{
    /**
     * Constructs a declare key=>value pair node.
     *
     * @param string    $key        Key
     * @param Node\Expr $value      Value
     * @param array     $attributes Additional attributes
     */
    public function __construct($key, Node\Expr $value, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
            ),
            $attributes
        );
    }
}