<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

/**
 * @property Node\Name|Expr $class Class name
 * @property string|Expr    $name  Method name
 * @property Node\Arg[]     $args  Arguments
 */
class StaticCall extends Expr
{
    /**
     * Constructs a static method call node.
     *
     * @param Node\Name|Expr $class      Class name
     * @param string|Expr    $name       Method name
     * @param Node\Arg[]     $args       Arguments
     * @param array          $attributes Additional attributes
     */
    public function __construct($class, $name, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'class' => $class,
                'name'  => $name,
                'args'  => $args
            ),
            $attributes
        );
    }
}