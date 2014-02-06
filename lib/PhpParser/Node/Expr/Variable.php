<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property string|Expr $name Name
 */
class Variable extends Expr
{
    /**
     * Constructs a variable node.
     *
     * @param string|Expr $name       Name
     * @param array                      $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct(
            array(
                 'name' => $name
            ),
            $attributes
        );
    }
}