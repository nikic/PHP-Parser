<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;

class Param extends PhpParser\BuilderAbstract
{
    protected $name;

    protected $default = null;

    /** @var string|Node\Name|Node\NullableType|null */
    protected $type = null;

    protected $byRef = false;

    protected $variadic = false;

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Sets default value for the parameter.
     *
     * @param mixed $value Default value to use
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDefault($value) {
        $this->default = $this->normalizeValue($value);

        return $this;
    }

    /**
     * Sets type hint for the parameter.
     *
     * @param string|Node\Name|Node\NullableType $type Type hint to use
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setTypeHint($type) {
        $this->type = $this->normalizeType($type);
        if ($this->type === 'void') {
            throw new \LogicException('Parameter type cannot be void');
        }

        return $this;
    }

    /**
     * Make the parameter accept the value by reference.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeByRef() {
        $this->byRef = true;

        return $this;
    }

    /**
     * Make the parameter variadic
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeVariadic() {
        $this->variadic = true;

        return $this;
    }

    /**
     * Returns the built parameter node.
     *
     * @return Node\Param The built parameter node
     */
    public function getNode() {
        return new Node\Param(
            $this->name, $this->default, $this->type, $this->byRef, $this->variadic
        );
    }
}
