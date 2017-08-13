<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class UnaryMinus extends Expr
{
    /** @var Expr Expression */
    public $expr;

    /**
     * Constructs a unary minus node.
     *
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = []) {
        parent::__construct($attributes);
        $this->expr = $expr;
    }

    public function getSubNodeNames() : array {
        return ['expr'];
    }
}
