<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Unset_ extends Node\Stmt {
    /** @var Node\Expr[] Variables to unset */
    public array $vars;

    private const SUBNODE_NAMES = ['vars'];

    /**
     * Constructs an unset node.
     *
     * @param Node\Expr[] $vars Variables to unset
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = []) {
        $this->attributes = $attributes;
        $this->vars = $vars;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }
    public function getType(): string {
        return 'Stmt_Unset';
    }
}
