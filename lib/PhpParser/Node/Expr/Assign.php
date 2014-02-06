<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr $var  Variable
 * @property Expr $expr Expression
 */
class Assign extends Expr
{
    /**
     * Constructs an assignment node.
     *
     * @param Expr  $var        Variable
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, Expr $expr, array $attributes = array()) {
        parent::__construct(
            array(
                'var'  => $var,
                'expr' => $expr
            ),
            $attributes
        );
    }
}