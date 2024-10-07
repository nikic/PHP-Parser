<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Switch_ extends Node\Stmt {
    /** @var Node\Expr Condition */
    public Node\Expr $cond;
    /** @var Case_[] Case list */
    public array $cases;

    private const SUBNODE_NAMES = ['cond', 'cases'];

    /**
     * Constructs a case node.
     *
     * @param Node\Expr $cond Condition
     * @param Case_[] $cases Case list
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $cases, array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->cases = $cases;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }
    public function getType(): string {
        return 'Stmt_Switch';
    }
}
