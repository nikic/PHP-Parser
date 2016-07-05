<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class ClassConst extends Node\Stmt
{
    /** @var int Modifiers */
    public $type;
    /** @var Node\Const_[] Constant declarations */
    public $consts;

    /**
     * Constructs a class const list node.
     *
     * @param Node\Const_[] $consts     Constant declarations
     * @param int           $type       Modifiers
     * @param array         $attributes Additional attributes
     */
    public function __construct(array $consts, $type = 0, array $attributes = array()) {
        if ($type & Class_::MODIFIER_STATIC) {
            throw new Error("Cannot use 'static' as constant modifier");
        }
        if ($type & Class_::MODIFIER_ABSTRACT) {
            throw new Error("Cannot use 'abstract' as constant modifier");
        }
        if ($type & Class_::MODIFIER_FINAL) {
            throw new Error("Cannot use 'final' as constant modifier");
        }

        parent::__construct($attributes);
        $this->type = $type;
        $this->consts = $consts;
    }

    public function getSubNodeNames() {
        return array('type', 'consts');
    }

    public function isPublic() {
        return ($this->type & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->type & Class_::VISIBILITY_MODIFER_MASK) === 0;
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
