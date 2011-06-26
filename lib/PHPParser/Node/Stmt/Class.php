<?php

/**
 * @property int            $type       Type
 * @property string         $name       Name
 * @property null|PHPParser_Node_Name $extends    Name of extended class
 * @property array          $implements Names of implemented interfaces
 * @property array          $stmts      Statements
 */
class PHPParser_Node_Stmt_Class extends PHPParser_Node_Stmt
{
    const MODIFIER_PUBLIC    =  1;
    const MODIFIER_PROTECTED =  2;
    const MODIFIER_PRIVATE   =  4;
    const MODIFIER_STATIC    =  8;
    const MODIFIER_ABSTRACT  = 16;
    const MODIFIER_FINAL     = 32;

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