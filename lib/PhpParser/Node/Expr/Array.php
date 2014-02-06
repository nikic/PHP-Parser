<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property ArrayItem[] $items Items
 */
class Array_ extends Expr
{
    /**
     * Constructs an array node.
     *
     * @param ArrayItem[] $items      Items of the array
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $items = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'items' => $items
            ),
            $attributes
        );
    }
}