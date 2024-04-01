<?php declare(strict_types=1);

namespace PhpParser;

/**
 * Modifiers used (as a bit mask) by various flags subnodes, for example on classes, functions,
 * properties and constants.
 */
final class Modifiers {
    public const PUBLIC    =  1;
    public const PROTECTED =  2;
    public const PRIVATE   =  4;
    public const STATIC    =  8;
    public const ABSTRACT  = 16;
    public const FINAL     = 32;
    public const READONLY  = 64;

    public const VISIBILITY_MASK = 1 | 2 | 4;

    /**
     * @internal
     */
    public static function verifyClassModifier(int $a, int $b): void {
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
    public static function verifyModifier(int $a, int $b): void {
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
}
