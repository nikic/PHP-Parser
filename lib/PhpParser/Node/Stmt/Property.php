<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

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
        parent::__construct(
            array(
                'type'  => $type,
                'props' => $props,
            ),
            $attributes
        );
    }

    public function isPublic() {
        return (bool) ($this->type & Class_::MODIFIER_PUBLIC);
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