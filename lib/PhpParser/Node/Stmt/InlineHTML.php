<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class InlineHTML extends Stmt {
    /** @var string String */
    public string $value;

    private const SUBNODE_NAMES = ['value'];

    /**
     * Constructs an inline HTML node.
     *
     * @param string $value String
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(string $value, array $attributes = []) {
        $this->attributes = $attributes;
        $this->value = $value;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }
    public function getType(): string {
        return 'Stmt_InlineHTML';
    }
}
