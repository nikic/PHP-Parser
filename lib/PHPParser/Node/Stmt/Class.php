<?php

/**
 * @property int                      $type       Type
 * @property string                   $name       Name
 * @property null|PHPParser_Node_Name $extends    Name of extended class
 * @property array                    $implements Names of implemented interfaces
 * @property array                    $stmts      Statements
 */
class PHPParser_Node_Stmt_Class extends PHPParser_Node_Stmt
{
    const MODIFIER_PUBLIC    =  1;
    const MODIFIER_PROTECTED =  2;
    const MODIFIER_PRIVATE   =  4;
    const MODIFIER_STATIC    =  8;
    const MODIFIER_ABSTRACT  = 16;
    const MODIFIER_FINAL     = 32;

    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, $line, $docComment);

        if ('self' == $this->name || 'parent' == $this->name) {
            throw new PHPParser_Error(sprintf('Cannot use "%s" as class name as it is reserved', $this->name));
        }

        if ('self' == $this->extends || 'parent' == $this->extends) {
            throw new PHPParser_Error(sprintf('Cannot use "%s" as class name as it is reserved', $this->extends));
        }

        foreach ($this->implements as $interface) {
            if ('self' == $interface || 'parent' == $interface) {
                throw new PHPParser_Error(sprintf('Cannot use "%s" as interface name as it is reserved', $interface));
            }
        }
    }

    public static function verifyModifier($a, $b) {
        if ($a & 7 && $b & 7) {
            throw new PHPParser_Error('Multiple access type modifiers are not allowed');
        }

        if ($a & self::MODIFIER_ABSTRACT && $b & self::MODIFIER_ABSTRACT) {
            throw new PHPParser_Error('Multiple abstract modifiers are not allowed');
        }

        if ($a & self::MODIFIER_STATIC && $b & self::MODIFIER_STATIC) {
            throw new PHPParser_Error('Multiple static modifiers are not allowed');
        }

        if ($a & self::MODIFIER_FINAL && $b & self::MODIFIER_FINAL) {
            throw new PHPParser_Error('Multiple final modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new PHPParser_Error('Cannot use the final modifier on an abstract class member');
        }
    }
}