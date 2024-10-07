<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Ternary extends Expr {
    /** @var Expr Condition */
    public Expr $cond;
    /** @var null|Expr Expression for true */
    public ?Expr $if;
    /** @var Expr Expression for false */
    public Expr $else;

    private const SUBNODE_NAMES = ['cond', 'if', 'else'];

    /**
     * Constructs a ternary operator node.
     *
     * @param Expr $cond Condition
     * @param null|Expr $if Expression for true
     * @param Expr $else Expression for false
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $cond, ?Expr $if, Expr $else, array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->if = $if;
        $this->else = $else;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Expr_Ternary';
    }
}
