<?php

/**
 * @property int            $type       Type
 * @property string         $name       Name
 * @property null|Node_Name $extends    Name of extended class
 * @property array          $implements Names of implemented interfaces
 * @property array          $stmts      Statements
 */
class Node_Stmt_Class extends Node_Stmt
{
    const MODIFIER_PUBLIC    =  1;
    const MODIFIER_PROTECTED =  2;
    const MODIFIER_PRIVATE   =  4;
    const MODIFIER_STATIC    =  8;
    const MODIFIER_ABSTRACT  = 16;
    const MODIFIER_FINAL     = 32;

    public static function verifyModifier($a, $b) {
        // TODO: Actually throw errors

        if ($a & 7 && $b & 7) {
            ('Multiple access type modifiers are not allowed');
        }

        if ($a & self::MODIFIER_ABSTRACT && $b & self::MODIFIER_ABSTRACT) {
            ('Multiple abstract modifiers are not allowed');
        }

        if ($a & self::MODIFIER_STATIC && $b & self::MODIFIER_STATIC) {
            ('Multiple static modifiers are not allowed');
        }

        if ($a & self::MODIFIER_FINAL && $b & self::MODIFIER_FINAL) {
            ('Multiple final modifiers are not allowed');
        }

        if (($a | $b)
            & (self::MODIFIER_ABSTRACT | self::MODIFIER_FINAL)
            == self::MODIFIER_ABSTRACT | self::MODIFIER_FINAL
        ) {
            ('Cannot use the final modifier on an abstract class member"');
        }
    }
}