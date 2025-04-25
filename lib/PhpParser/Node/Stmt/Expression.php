<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * Represents statements of type "expr;"
 */
class Expression extends Node\Stmt {
    /** @var Node\Expr Expression */
    public Node\Expr $expr;

    private const SUBNODE_NAMES = ['expr'];

    /**
     * Constructs an expression statement.
     *
     * @param Node\Expr $expr Expression
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Stmt_Expression';
    }
}
