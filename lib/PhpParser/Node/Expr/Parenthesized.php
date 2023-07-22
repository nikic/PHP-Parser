<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Parenthesized extends Expr
{
    /** @var Expr $expr */
    public $expr;

    /**
     * Constructs a parenthesized expression.
     *
     * @param Expr $expr  Expression to be parenthesized
     * @param array       $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }

    public function getSubNodeNames() : array {
        return ['expr'];
    }
    
    public function getType() : string {
        return 'Expr_Parenthesized';
    }
}
