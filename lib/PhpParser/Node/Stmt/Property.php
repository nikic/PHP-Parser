<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class Property extends Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var PropertyProperty[] Properties */
    public $props;

    /** @deprecated Use $flags instead */
    public $type;

    /**
     * Constructs a class property list node.
     *
     * @param int                $flags      Modifiers
     * @param PropertyProperty[] $props      Properties
     * @param array              $attributes Additional attributes
     */
    public function __construct($flags, array $props, array $attributes = array()) {
        if ($flags & Class_::MODIFIER_ABSTRACT) {
            throw new Error('Properties cannot be declared abstract');
        }

        if ($flags & Class_::MODIFIER_FINAL) {
            throw new Error('Properties cannot be declared final');
        }

        parent::__construct($attributes);
        $this->flags = $flags;
        $this->type = $flags;
        $this->props = $props;
    }

    public function getSubNodeNames() {
        return array('flags', 'props');
    }

    public function isPublic() {
        return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->flags & Class_::VISIBILITY_MODIFER_MASK) === 0;
    }

    public function isProtected() {
        return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
    }

    public function isPrivate() {
        return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
    }

    public function isStatic() {
        return (bool) ($this->flags & Class_::MODIFIER_STATIC);
    }
}
