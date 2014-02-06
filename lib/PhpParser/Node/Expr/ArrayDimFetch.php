<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr      $var Variable
 * @property null|Expr $dim Array index / dim
 */
class ArrayDimFetch extends Expr
{
    /**
     * Constructs an array index fetch node.
     *
     * @param Expr      $var        Variable
     * @param null|Expr $dim        Array index / dim
     * @param array     $attributes Additional attributes
     */
    public function __construct(Expr $var, Expr $dim = null, array $attributes = array()) {
        parent::__construct(
            array(
                'var' => $var,
                'dim' => $dim
            ),
            $attributes
        );
    }
}