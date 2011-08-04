<?php

/**
 * @property array $parts Parts of the name
 */
class PHPParser_Node_Name extends PHPParser_NodeAbstract
{
    const ABSOLUTE = 1;
    const RELATIVE = 2;

    protected $resolveType;

    public function resolveType($type) {
        $this->resolveType = $type;
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