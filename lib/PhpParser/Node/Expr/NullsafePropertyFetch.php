<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;

class NullsafePropertyFetch extends Expr {
    /** @var Expr Variable holding object */
    public Expr $var;
    /** @var Identifier|Expr Property name */
    public Node $name;

    private const SUBNODE_NAMES = ['var', 'name'];

    /**
     * Constructs a nullsafe property fetch node.
     *
     * @param Expr $var Variable holding object
     * @param string|Identifier|Expr $name Property name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, $name, array $attributes = []) {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'Expr_NullsafePropertyFetch';
    }
}
