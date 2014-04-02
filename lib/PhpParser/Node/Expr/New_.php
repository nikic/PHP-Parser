<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

/**
 * @property Node\Name|Expr $class Class name
 * @property Node\Arg[]     $args  Arguments
 */
class New_ extends Expr
{
    /**
     * Constructs a function call node.
     *
     * @param Node\Name|Expr $class      Class name
     * @param Node\Arg[]     $args       Arguments
     * @param array          $attributes Additional attributes
     */
    public function __construct($class, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'class' => $class,
                'args'  => $args
            ),
            $attributes
        );
    }
}