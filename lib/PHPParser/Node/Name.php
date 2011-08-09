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
     * Constructs a name node.
     *
     * @param string|array $parts      Parts of the name (or name as string)
     * @param int          $type       Resolve type
     * @param int          $line       Line
     * @param null|string  $docComment Nearest doc comment
     */
    public function __construct($parts, $type = self::NORMAL, $line = -1, $docComment = null) {
        if (!is_array($parts)) {
            $parts = explode('\\', $parts);
        }

        parent::__construct(
            array(
                'parts' => $parts,
                'type'  => $type
            ),
            $line, $docComment
        );
    }

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
     * namespace separator.
     *
     * @return string String representation
     */
    public function __toString() {
        return $this->toString('\\');
    }

    /**
     * Sets the whole name.
     *
     * @param string|array|self $name The name to set the whole name to
     */
    public function set($name) {
        $this->parts = $this->prepareName($name);
    }

    /**
     * Prepends a name to this name.
     *
     * @param string|array|self $name Name to prepend
     */
    public function prepend($name) {
        $this->parts = array_merge($this->prepareName($name), $this->parts);
    }

    /**
     * Appends a name to this name.
     *
     * @param string|array|self $name Name to append
     */
    public function append($name) {
        $this->parts = array_merge($this->prepareName($name), $name->parts);
    }

    /**
     * Sets the first part of the name.
     *
     * @param string|array|self $name The name to set the first part to
     */
    public function setFirst($name) {
        $this->parts = array_merge($this->prepareName($name), array_slice($this->parts, 1));
    }

    /**
     * Sets the last part of the name.
     *
     * @param string|array|self $name The name to set the last part to
     */
    public function setLast($name) {
        $this->parts = array_merge($this->prepareName($name), array_slice($this->parts, 0, -1));
    }

    /**
     * Prepares a (string, array or Name node) name for use in name changing methods by converting
     * it to an array.
     *
     * @param string|array|self $name Name to prepare
     *
     * @return array Prepared name
     */
    protected function prepareName($name) {
        if (is_string($name)) {
            return explode('\\', $name);
        } elseif ($name instanceof self) {
            return $name->parts;
        } elseif (!is_array($name)) {
            throw new InvalidArgumentException(
                'When changing a name you need to pass either a string and array or a Name node'
            );
        }
    }
}