<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Error;
use PhpParser\Node;

class Class_ extends ClassLike
{
    const MODIFIER_PUBLIC    =  1;
    const MODIFIER_PROTECTED =  2;
    const MODIFIER_PRIVATE   =  4;
    const MODIFIER_STATIC    =  8;
    const MODIFIER_ABSTRACT  = 16;
    const MODIFIER_FINAL     = 32;

    const VISIBILITY_MODIFER_MASK = 7; // 1 | 2 | 4

    /** @var int Type */
    public $flags;
    /** @var null|Node\Name Name of extended class */
    public $extends;
    /** @var Node\Name[] Names of implemented interfaces */
    public $implements;

    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param string|null $name       Name
     * @param array       $subNodes   Array of the following optional subnodes:
     *                                'flags'      => 0      : Flags
     *                                'extends'    => null   : Name of extended class
     *                                'implements' => array(): Names of implemented interfaces
     *                                'stmts'      => array(): Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->flags = isset($subNodes['flags']) ? $subNodes['flags']
            : (isset($subNodes['type']) ? $subNodes['type'] : 0);
        $this->name = $name;
        $this->extends = isset($subNodes['extends']) ? $subNodes['extends'] : null;
        $this->implements = isset($subNodes['implements']) ? $subNodes['implements'] : array();
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();
    }

    public function getSubNodeNames() {
        return array('flags', 'name', 'extends', 'implements', 'stmts');
    }

    /**
     * @return bool
     */
    public function isAbstract() {
        return (bool) ($this->flags & self::MODIFIER_ABSTRACT);
    }

    /**
     * @return bool
     */
    public function isFinal() {
        return (bool) ($this->flags & self::MODIFIER_FINAL);
    }

    /**
     * @return bool
     */
    public function isAnonymous() {
        return null === $this->name;
    }

    /**
     * @internal
     */
    public static function verifyModifier($a, $b) {
        if ($a & self::VISIBILITY_MODIFER_MASK && $b & self::VISIBILITY_MODIFER_MASK) {
            throw new Error('Multiple access type modifiers are not allowed');
        }

        if ($a & self::MODIFIER_ABSTRACT && $b & self::MODIFIER_ABSTRACT) {
            throw new Error('Multiple abstract modifiers are not allowed');
        }

        if ($a & self::MODIFIER_STATIC && $b & self::MODIFIER_STATIC) {
            throw new Error('Multiple static modifiers are not allowed');
        }

        if ($a & self::MODIFIER_FINAL && $b & self::MODIFIER_FINAL) {
            throw new Error('Multiple final modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class member');
        }
    }
}
