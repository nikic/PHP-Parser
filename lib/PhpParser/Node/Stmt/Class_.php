<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property int            $type       Type
 * @property string         $name       Name
 * @property null|Node\Name $extends    Name of extended class
 * @property Node\Name[]    $implements Names of implemented interfaces
 * @property Node[]         $stmts      Statements
 */
class Class_ extends Node\Stmt
{
    const MODIFIER_PUBLIC    =  1;
    const MODIFIER_PROTECTED =  2;
    const MODIFIER_PRIVATE   =  4;
    const MODIFIER_STATIC    =  8;
    const MODIFIER_ABSTRACT  = 16;
    const MODIFIER_FINAL     = 32;

    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param string      $name       Name
     * @param array       $subNodes   Array of the following optional subnodes:
     *                                'type'       => 0      : Type
     *                                'extends'    => null   : Name of extended class
     *                                'implements' => array(): Names of implemented interfaces
     *                                'stmts'      => array(): Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'type'       => isset($subNodes['type'])       ? $subNodes['type']       : 0,
                'name'       => $name,
                'extends'    => isset($subNodes['extends'])    ? $subNodes['extends']    : null,
                'implements' => isset($subNodes['implements']) ? $subNodes['implements'] : array(),
                'stmts'      => isset($subNodes['stmts'])      ? $subNodes['stmts']      : array(),
            ),
            $attributes
        );

        if (isset(self::$specialNames[(string) $this->name])) {
            throw new Error(sprintf('Cannot use \'%s\' as class name as it is reserved', $this->name));
        }

        if (isset(self::$specialNames[(string) $this->extends])) {
            throw new Error(sprintf('Cannot use \'%s\' as class name as it is reserved', $this->extends));
        }

        foreach ($this->implements as $interface) {
            if (isset(self::$specialNames[(string) $interface])) {
                throw new Error(sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface));
            }
        }
    }

    public function isAbstract() {
        return (bool) ($this->type & self::MODIFIER_ABSTRACT);
    }

    public function isFinal() {
        return (bool) ($this->type & self::MODIFIER_FINAL);
    }

    public function getMethods() {
        $methods = array();
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $methods[] = $stmt;
            }
        }
        return $methods;
    }

    /**
     * @internal
     */
    public static function verifyModifier($a, $b) {
        if ($a & 7 && $b & 7) {
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
