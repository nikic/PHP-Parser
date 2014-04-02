<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property null|Expr $value Value expression
 * @property null|Expr $key   Key expression
 */
class Yield_ extends Expr
{
    /**
     * Constructs a yield expression node.
     *
     * @param null|Expr $value      Value expression
     * @param null|Expr $key        Key expression
     * @param array     $attributes Additional attributes
     */
    public function __construct(Expr $value = null, Expr $key = null, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
            ),
            $attributes
        );
    }
}