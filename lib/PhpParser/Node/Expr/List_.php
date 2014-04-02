<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr[] $vars List of variables to assign to
 */
class List_ extends Expr
{
    /**
     * Constructs a list() destructuring node.
     *
     * @param Expr[] $vars       List of variables to assign to
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(
            array(
                'vars' => $vars,
            ),
            $attributes
        );
    }
}