<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class PreDec extends Expr {
    /** @var Expr Variable */
    public Expr $var;

    private const SUBNODE_NAMES = ['var'];

    /**
     * Constructs a pre decrement node.
     *
     * @param Expr $var Variable
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = []) {
        $this->attributes = $attributes;
        $this->var = $var;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Expr_PreDec';
    }
}
