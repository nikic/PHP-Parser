<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\Attribute;

class ClassConst extends Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var Node\Const_[] Constant declarations */
    public $consts;
    /** @var Attribute[] */
    public $phpAttributes;

    /**
     * Constructs a class const list node.
     *
     * @param Node\Const_[] $consts        Constant declarations
     * @param int           $flags         Modifiers
     * @param array         $attributes    Additional attributes
     * @param Attribute[]   $phpAttributes PHP attributes
     */
    public function __construct(
        array $consts,
        int $flags = 0,
        array $attributes = [],
        array $phpAttributes = []
    ) {
        $this->attributes = $attributes;
        $this->flags = $flags;
        $this->consts = $consts;
        $this->phpAttributes = $phpAttributes;
    }

    public function getSubNodeNames() : array {
        return ['phpAttributes', 'flags', 'consts'];
    }

    /**
     * Whether constant is explicitly or implicitly public.
     *
     * @return bool
     */
    public function isPublic() : bool {
        return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->flags & Class_::VISIBILITY_MODIFIER_MASK) === 0;
    }

    /**
     * Whether constant is protected.
     *
     * @return bool
     */
    public function isProtected() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
    }

    /**
     * Whether constant is private.
     *
     * @return bool
     */
    public function isPrivate() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
    }

    public function getType() : string {
        return 'Stmt_ClassConst';
    }
}
