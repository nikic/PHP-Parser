<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Property extends Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var PropertyProperty[] Properties */
    public $props;

    /**
     * Constructs a class property list node.
     *
     * @param int                $flags      Modifiers
     * @param PropertyProperty[] $props      Properties
     * @param array              $attributes Additional attributes
     */
    public function __construct($flags, array $props, array $attributes = array()) {
        parent::__construct($attributes);
        $this->flags = $flags;
        $this->props = $props;
    }

    public function getSubNodeNames() {
        return array('flags', 'props');
    }

    /**
     * Whether the property is explicitly or implicitly public.
     *
     * @return bool
     */
    public function isPublic() {
        return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->flags & Class_::VISIBILITY_MODIFER_MASK) === 0;
    }

    /**
     * Whether the property is protected.
     *
     * @return bool
     */
    public function isProtected() {
        return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
    }

    /**
     * Whether the property is private.
     *
     * @return bool
     */
    public function isPrivate() {
        return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
    }

    /**
     * Whether the property is static.
     *
     * @return bool
     */
    public function isStatic() {
        return (bool) ($this->flags & Class_::MODIFIER_STATIC);
    }
}
