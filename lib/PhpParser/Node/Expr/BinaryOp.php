<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr $left  The left hand side expression
 * @property Expr $right The right hand side expression
 */
class BinaryOp extends Expr
{
    /**
     * Constructs a bitwise and node.
     *
     * @param Expr  $left       The left hand side expression
     * @param Expr  $right      The right hand side expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $left, Expr $right, array $attributes = array()) {
        parent::__construct(
            array(
                'left'  => $left,
                'right' => $right
            ),
            $attributes
        );
    }
}