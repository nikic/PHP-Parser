<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Variable extends Expr {
    /** @var string|Expr Name */
    public $name;

    private const SUBNODE_NAMES = ['name'];

    /**
     * Constructs a variable node.
     *
     * @param string|Expr $name Name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = $name;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Expr_Variable';
    }
}
