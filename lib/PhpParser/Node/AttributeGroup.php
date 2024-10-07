<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class AttributeGroup extends NodeAbstract {
    /** @var Attribute[] Attributes */
    public array $attrs;

    private const SUBNODE_NAMES = ['attrs'];

    /**
     * @param Attribute[] $attrs PHP attributes
     * @param array<string, mixed> $attributes Additional node attributes
     */
    public function __construct(array $attrs, array $attributes = []) {
        $this->attributes = $attributes;
        $this->attrs = $attrs;
    }

    /**
     * @return self::SUBNODE_NAMES
     */
    public function getSubNodeNames(): array {
        return self::SUBNODE_NAMES;
    }

    public function getType(): string {
        return 'AttributeGroup';
    }
}
