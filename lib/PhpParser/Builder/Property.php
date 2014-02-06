<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node\Stmt;

class Property extends PhpParser\BuilderAbstract
{
    protected $name;

    protected $type;
    protected $default;

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     */
    public function __construct($name) {
        $this->name = $name;

        $this->type = 0;
        $this->default = null;
    }

    /**
     * Makes the property public.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->setModifier(Stmt\Class_::MODIFIER_PUBLIC);

        return $this;
    }

    /**
     * Makes the property protected.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->setModifier(Stmt\Class_::MODIFIER_PROTECTED);

        return $this;
    }

    /**
     * Makes the property private.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->setModifier(Stmt\Class_::MODIFIER_PRIVATE);

        return $this;
    }

    /**
     * Makes the property static.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeStatic() {
        $this->setModifier(Stmt\Class_::MODIFIER_STATIC);

        return $this;
    }

    /**
     * Sets default value for the property.
     *
     * @param mixed $value Default value to use
     *
     * @return self The builder instance (for fluid interface)
     */
    public function setDefault($value) {
        $this->default = $this->normalizeValue($value);

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return Stmt\Property The built property node
     */
    public function getNode() {
        return new Stmt\Property(
            $this->type !== 0 ? $this->type : Stmt\Class_::MODIFIER_PUBLIC,
            array(
                new Stmt\PropertyProperty($this->name, $this->default)
            )
        );
    }
}