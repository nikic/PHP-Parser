<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * Represents the "?" argument placeholder of the partial function application syntax,
 * e.g. the "?" in "foo(?)". It can only occur as the value of an Arg node.
 */
class Placeholder extends Expr {
    /**
     * Create a "?" argument placeholder (partial function application syntax).
     *
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $attributes = []) {
        $this->attributes = $attributes;
    }

    public function getSubNodeNames(): array {
        return [];
    }

    public function getType(): string {
        return 'Expr_Placeholder';
    }
}
