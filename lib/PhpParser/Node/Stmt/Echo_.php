<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Echo_ extends Node\Stmt {
    /** @var Node\Expr[] Expressions */
    public array $exprs;

    private const SUBNODE_NAMES = ['exprs'];

    /**
     * Constructs an echo node.
     *
     * @param Node\Expr[] $exprs Expressions
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $exprs, array $attributes = []) {
        $this->attributes = $attributes;
        $this->exprs = $exprs;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Stmt_Echo';
    }
}
