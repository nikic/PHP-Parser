<?php declare(strict_types=1);

namespace PhpParser\Node;

class IntersectionType extends ComplexType {
    /** @var (Identifier|Name)[] Types */
    public array $types;

    private const SUBNODE_NAMES = ['types'];

    /**
     * Constructs an intersection type.
     *
     * @param (Identifier|Name)[] $types Types
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $types, array $attributes = []) {
        $this->attributes = $attributes;
        $this->types = $types;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'IntersectionType';
    }
}
