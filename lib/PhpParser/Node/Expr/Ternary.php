<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr      $cond Condition
 * @property null|Expr $if   Expression for true
 * @property Expr      $else Expression for false
 */
class Ternary extends Expr
{
    /**
     * Constructs a ternary operator node.
     *
     * @param Expr      $cond       Condition
     * @param null|Expr $if         Expression for true
     * @param Expr      $else       Expression for false
     * @param array                    $attributes Additional attributes
     */
    public function __construct(Expr $cond, $if, Expr $else, array $attributes = array()) {
        parent::__construct(
            array(
                'cond' => $cond,
                'if'   => $if,
                'else' => $else
            ),
            $attributes
        );
    }
}