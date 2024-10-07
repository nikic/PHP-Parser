<?php declare(strict_types=1);

namespace PhpParser\Node;

class UnionType extends ComplexType {
    /** @var (Identifier|Name|IntersectionType)[] Types */
    public array $types;

    private const SUBNODE_NAMES = ['types'];

    /**
     * Constructs a union type.
     *
     * @param (Identifier|Name|IntersectionType)[] $types Types
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
        return 'UnionType';
    }
}
