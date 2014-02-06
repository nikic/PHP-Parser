<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * @property string              $name  Name
 * @property Expr $value Value
 */
class Const_ extends NodeAbstract
{
    /**
     * Constructs a const node for use in class const and const statements.
     *
     * @param string  $name       Name
     * @param Expr    $value      Value
     * @param array   $attributes Additional attributes
     */
    public function __construct($name, Expr $value, array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name,
                'value' => $value,
            ),
            $attributes
        );
    }
}