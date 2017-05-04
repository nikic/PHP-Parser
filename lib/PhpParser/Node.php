<?php

namespace PhpParser;

interface Node
{
    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() : string;

    /**
     * Gets the names of the sub nodes.
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames() : array;

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine() : int;

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|Comment\Doc Doc comment object or null
     */
    public function getDocComment();

    /**
     * Sets the doc comment of the node.
     *
     * This will either replace an existing doc comment or add it to the comments array.
     *
     * @param Comment\Doc $docComment Doc comment to set
     */
    public function setDocComment(Comment\Doc $docComment);

    /**
     * Sets an attribute on a node.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute(string $key, $value);

    /**
     * Returns whether an attribute exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key) : bool;

    /**
     * Returns the value of an attribute.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function &getAttribute(string $key, $default = null);

    /**
     * Returns all the attributes of this node.
     *
     * @return array
     */
    public function getAttributes() : array;

    /**
     * Replaces all the attributes of this node.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes);
}
