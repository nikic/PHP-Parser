<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\ComplexType;

class Property implements PhpParser\Builder {
    /** @var string */
    protected $name;
    /** @var int */
    protected $flags = 0;
    /** @var Node\Expr|null */
    protected $default = null;
    /** @var array<string, mixed> */
    protected $attributes = [];
    /** @var null|Identifier|Name|ComplexType */
    protected $type;
    /** @var list<Node\AttributeGroup> */
    protected $attributeGroups = [];

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Makes the property public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PUBLIC);

        return $this;
    }

    /**
     * Makes the property protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED);

        return $this;
    }

    /**
     * Makes the property private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE);

        return $this;
    }

    /**
     * Makes the property static.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeStatic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::STATIC);

        return $this;
    }

    /**
     * Makes the property readonly.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeReadonly() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::READONLY);

        return $this;
    }

    /**
     * Sets default value for the property.
     *
     * @param mixed $value Default value to use
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDefault($value) {
        $this->default = BuilderHelpers::normalizeValue($value);

        return $this;
    }

    /**
     * Sets doc comment for the property.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment) {
        $this->attributes = [
            'comments' => [BuilderHelpers::normalizeDocComment($docComment)]
        ];

        return $this;
    }

    /**
     * Sets the property type for PHP 7.4+.
     *
     * @param string|Name|Identifier|ComplexType $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = BuilderHelpers::normalizeType($type);

        return $this;
    }

    /**
     * Adds an attribute group.
     *
     * @param Node\Attribute|Node\AttributeGroup $attribute
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addAttribute($attribute) {
        $this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return Stmt\Property The built property node
     */
    public function getNode(): PhpParser\Node {
        return new Stmt\Property(
            $this->flags !== 0 ? $this->flags : Modifiers::PUBLIC,
            [
                new Node\PropertyItem($this->name, $this->default)
            ],
            $this->attributes,
            $this->type,
            $this->attributeGroups
        );
    }
}
