<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Isset_ extends Expr {
    /** @var Expr[] Variables */
    public array $vars;

    private const SUBNODE_NAMES = ['vars'];

    /**
     * Constructs an array node.
     *
     * @param Expr[] $vars Variables
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
        return 'Expr_Isset';
    }
}
