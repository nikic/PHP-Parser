<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * Represents the "?" argument placeholder of the partial function application syntax,
 * e.g. the "?" in "foo(?)". Like VariadicPlaceholder, it occurs in the argument list
 * of a call, in place of an ordinary Arg.
 */
class Placeholder extends NodeAbstract {
    /** @var Identifier|null Parameter name (for named placeholders) */
    public ?Identifier $name;

    /**
     * Create a "?" argument placeholder (partial function application syntax).
     *
     * @param Identifier|null $name Parameter name (for named placeholders)
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(?Identifier $name = null, array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = $name;
    }

    public function getSubNodeNames(): array {
        return ['name'];
    }

    public function getType(): string {
        return 'Placeholder';
    }
}
