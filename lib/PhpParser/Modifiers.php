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

    private const TO_STRING_MAP = [
        self::PUBLIC  => 'public',
        self::PROTECTED => 'protected',
        self::PRIVATE => 'private',
        self::STATIC  => 'static',
        self::ABSTRACT => 'abstract',
        self::FINAL  => 'final',
        self::READONLY  => 'readonly',
    ];

    public static function toString(int $modifier): string {
        if (!isset(self::TO_STRING_MAP[$modifier])) {
            throw new \InvalidArgumentException("Unknown modifier $modifier");
        }
        return self::TO_STRING_MAP[$modifier];
    }

    /**
     * @internal
     */
    public static function verifyClassModifier(int $a, int $b): void {
        foreach ([Modifiers::ABSTRACT, Modifiers::FINAL, Modifiers::READONLY] as $modifier) {
            if ($a & $modifier && $b & $modifier) {
                throw new Error(
                    'Multiple ' . self::toString($modifier) . ' modifiers are not allowed');
            }
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

        foreach ([Modifiers::ABSTRACT, Modifiers::STATIC, Modifiers::FINAL, Modifiers::READONLY] as $modifier) {
            if ($a & $modifier && $b & $modifier) {
                throw new Error(
                    'Multiple ' . self::toString($modifier) . ' modifiers are not allowed');
            }
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class member');
        }
    }
}
