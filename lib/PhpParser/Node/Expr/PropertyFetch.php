<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr        $var  Variable holding object
 * @property string|Expr $name Property Name
 */
class PropertyFetch extends Expr
{
    /**
     * Constructs a function call node.
     *
     * @param Expr        $var        Variable holding object
     * @param string|Expr $name       Property name
     * @param array       $attributes Additional attributes
     */
    public function __construct(Expr $var, $name, array $attributes = array()) {
        parent::__construct(
            array(
                'var'  => $var,
                'name' => $name
            ),
            $attributes
        );
    }
}