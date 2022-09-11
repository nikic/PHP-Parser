<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Error;
use PhpParser\Modifiers;
use PhpParser\Node;

class Class_ extends ClassLike {
    /** @deprecated Use Modifiers::PUBLIC instead */
    public const MODIFIER_PUBLIC    =  1;
    /** @deprecated Use Modifiers::PROTECTED instead */
    public const MODIFIER_PROTECTED =  2;
    /** @deprecated Use Modifiers::PRIVATE instead */
    public const MODIFIER_PRIVATE   =  4;
    /** @deprecated Use Modifiers::STATIC instead */
    public const MODIFIER_STATIC    =  8;
    /** @deprecated Use Modifiers::ABSTRACT instead */
    public const MODIFIER_ABSTRACT  = 16;
    /** @deprecated Use Modifiers::FINAL instead */
    public const MODIFIER_FINAL     = 32;
    /** @deprecated Use Modifiers::READONLY instead */
    public const MODIFIER_READONLY  = 64;

    /** @deprecated Use Modifiers::VISIBILITY_MASK instead */
    public const VISIBILITY_MODIFIER_MASK = 7; // 1 | 2 | 4

    /** @var int Modifiers */
    public $flags;
    /** @var null|Node\Name Name of extended class */
    public $extends;
    /** @var Node\Name[] Names of implemented interfaces */
    public $implements;

    /**
     * Constructs a class node.
     *
     * @param string|Node\Identifier|null $name Name
     * @param array       $subNodes   Array of the following optional subnodes:
     *                                'flags'       => 0      : Flags
     *                                'extends'     => null   : Name of extended class
     *                                'implements'  => array(): Names of implemented interfaces
     *                                'stmts'       => array(): Statements
     *                                'attrGroups'  => array(): PHP attribute groups
     * @param array       $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->flags = $subNodes['flags'] ?? $subNodes['type'] ?? 0;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->extends = $subNodes['extends'] ?? null;
        $this->implements = $subNodes['implements'] ?? [];
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'flags', 'name', 'extends', 'implements', 'stmts'];
    }

    /**
     * Whether the class is explicitly abstract.
     *
     * @return bool
     */
    public function isAbstract(): bool {
        return (bool) ($this->flags & Modifiers::ABSTRACT);
    }

    /**
     * Whether the class is final.
     *
     * @return bool
     */
    public function isFinal(): bool {
        return (bool) ($this->flags & Modifiers::FINAL);
    }

    public function isReadonly(): bool {
        return (bool) ($this->flags & Modifiers::READONLY);
    }

    /**
     * Whether the class is anonymous.
     *
     * @return bool
     */
    public function isAnonymous(): bool {
        return null === $this->name;
    }

    /**
     * @internal
     */
    public static function verifyClassModifier(int $a, int $b) {
        if ($a & Modifiers::ABSTRACT && $b & Modifiers::ABSTRACT) {
            throw new Error('Multiple abstract modifiers are not allowed');
        }

        if ($a & Modifiers::FINAL && $b & Modifiers::FINAL) {
            throw new Error('Multiple final modifiers are not allowed');
        }

        if ($a & Modifiers::READONLY && $b & Modifiers::READONLY) {
            throw new Error('Multiple readonly modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class');
        }
    }

    /**
     * @internal
     */
    public static function verifyModifier(int $a, int $b) {
        if ($a & Modifiers::VISIBILITY_MASK && $b & Modifiers::VISIBILITY_MASK) {
            throw new Error('Multiple access type modifiers are not allowed');
        }

        if ($a & Modifiers::ABSTRACT && $b & Modifiers::ABSTRACT) {
            throw new Error('Multiple abstract modifiers are not allowed');
        }

        if ($a & Modifiers::STATIC && $b & Modifiers::STATIC) {
            throw new Error('Multiple static modifiers are not allowed');
        }

        if ($a & Modifiers::FINAL && $b & Modifiers::FINAL) {
            throw new Error('Multiple final modifiers are not allowed');
        }

        if ($a & Modifiers::READONLY && $b & Modifiers::READONLY) {
            throw new Error('Multiple readonly modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class member');
        }
    }

    public function getType(): string {
        return 'Stmt_Class';
    }
}
