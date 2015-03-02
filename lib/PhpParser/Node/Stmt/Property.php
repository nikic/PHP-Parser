<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property int                $type  Modifiers
 * @property PropertyProperty[] $props Properties
 */
class Property extends Node\Stmt
{
    /**
     * Constructs a class property list node.
     *
     * @param int                $type       Modifiers
     * @param PropertyProperty[] $props      Properties
     * @param array              $attributes Additional attributes
     */
    public function __construct($type, array $props, array $attributes = array()) {
        if ($type & Class_::MODIFIER_ABSTRACT) {
            throw new Error('Properties cannot be declared abstract');
        }

        if ($type & Class_::MODIFIER_FINAL) {
            throw new Error('Properties cannot be declared final');
        }

        parent::__construct(
            array(
                'type'  => $type,
                'props' => $props,
            ),
            $attributes
        );
    }

    public function isPublic() {
        return ($this->type & Class_::MODIFIER_PUBLIC) !== 0 || $this->type === 0;
    }

    public function isProtected() {
        return (bool) ($this->type & Class_::MODIFIER_PROTECTED);
    }

    public function isPrivate() {
        return (bool) ($this->type & Class_::MODIFIER_PRIVATE);
    }

    public function isStatic() {
        return (bool) ($this->type & Class_::MODIFIER_STATIC);
    }
}
