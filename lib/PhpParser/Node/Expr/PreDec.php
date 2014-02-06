<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr $var Variable
 */
class PreDec extends Expr
{
    /**
     * Constructs a pre decrement node.
     *
     * @param Expr  $var        Variable
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = array()) {
        parent::__construct(
            array(
                'var' => $var
            ),
            $attributes
        );
    }
}