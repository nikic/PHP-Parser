<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class ClassConst extends Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var Node\Const_[] Constant declarations */
    public $consts;

    /**
     * Constructs a class const list node.
     *
     * @param Node\Const_[] $consts     Constant declarations
     * @param int           $flags      Modifiers
     * @param array         $attributes Additional attributes
     */
    public function __construct(array $consts, $flags = 0, array $attributes = array()) {
        parent::__construct($attributes);
        $this->flags = $flags;
        $this->consts = $consts;
    }

    public function getSubNodeNames() {
        return array('flags', 'consts');
    }

    /**
     * @return bool
     */
    public function isPublic() {
        return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->flags & Class_::VISIBILITY_MODIFER_MASK) === 0;
    }

    /**
     * @return bool
     */
    public function isProtected() {
        return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
    }

    /**
     * @return bool
     */
    public function isPrivate() {
        return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
    }

    /**
     * @return bool
     */
    public function isStatic() {
        return (bool) ($this->flags & Class_::MODIFIER_STATIC);
    }
}
