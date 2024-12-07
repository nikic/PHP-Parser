<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;

class Label extends Stmt {
    /** @var Identifier Name */
    public Identifier $name;

    private const SUBNODE_NAMES = ['name'];

    /**
     * Constructs a label node.
     *
     * @param string|Identifier $name Name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }
    public function getType(): string {
        return 'Stmt_Label';
    }
}
