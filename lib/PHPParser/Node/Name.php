<?php

/**
 * @property array $parts Parts of the name
 * @property int   $type  Resolve type (self::NORMAL, self::FULLY_QUALIFIED or self::RELATIVE)
 */
class PHPParser_Node_Name extends PHPParser_NodeAbstract
{
    const NORMAL          = 0;
    const FULLY_QUALIFIED = 1;
    const RELATIVE        = 2;

    /**
     * Gets the first part of the name, i.e. everything before the first namespace separator.
     *
     * @return string First part of the name
     */
    public function getFirst() {
        return $this->parts[0];
    }

    /**
     * Gets the last part of the name, i.e. everything after the last namespace separator.
     *
     * @return string Last part of the name
     */
    public function getLast() {
        return $this->parts[count($this->parts) - 1];
    }

    /**
     * Checks whether the name is unqualified. (E.g. Name)
     *
     * @return bool Whether the name is unqualified
     */
    public function isUnqualified() {
        return self::NORMAL == $this->type && 1 == count($this->parts);
    }

    /**
     * Checks whether the name is qualified. (E.g. Name\Name)
     *
     * @return bool Whether the name is qualified
     */
    public function isQualified() {
        return self::NORMAL == $this->type && 1 < count($this->parts);
    }

    /**
     * Checks whether the name is fully qualified. (E.g. \Name)
     *
     * @return bool Whether the name is fully qualified
     */
    public function isFullyQualified() {
        return self::FULLY_QUALIFIED == $this->type;
    }

    /**
     * Checks whether the name is explicitly relative to the current namespace. (E.g. namespace\Name)
     *
     * @return bool Whether the name is fully qualified
     */
    public function isRelative() {
        return self::RELATIVE == $this->type;
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with a separator.
     *
     * @param string $separator The separator to use (defaults to the namespace separator \)
     *
     * @return string String representation
     */
    public function toString($separator = '\\') {
        return implode($separator, $this->parts);
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with the
     * namespace separator \ (backslash).
     *
     * @return string String representation
     */
    public function __toString() {
        return $this->toString('\\');
    }
}