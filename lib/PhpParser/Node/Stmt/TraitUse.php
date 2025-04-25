<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class TraitUse extends Node\Stmt {
    /** @var Node\Name[] Traits */
    public array $traits;
    /** @var TraitUseAdaptation[] Adaptations */
    public array $adaptations;

    private const SUBNODE_NAMES = ['traits', 'adaptations'];

    /**
     * Constructs a trait use node.
     *
     * @param Node\Name[] $traits Traits
     * @param TraitUseAdaptation[] $adaptations Adaptations
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $traits, array $adaptations = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->traits = $traits;
        $this->adaptations = $adaptations;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }
    public function getType(): string {
        return 'Stmt_TraitUse';
    }
}
