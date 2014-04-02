<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property null|Node\Expr $expr Expression
 */
class Return_ extends Node\Stmt
{
    /**
     * Constructs a return node.
     *
     * @param null|Node\Expr $expr       Expression
     * @param array          $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr = null, array $attributes = array()) {
        parent::__construct(
            array(
                'expr' => $expr,
            ),
            $attributes
        );
    }
}