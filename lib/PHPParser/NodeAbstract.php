<?php

abstract class PHPParser_NodeAbstract implements PHPParser_Node, IteratorAggregate
{
    protected $subNodes;
    protected $line;
    protected $docComment;
    protected $attributes;

    /**
     * Creates a Node.
     *
     * @param array       $subNodes   Array of sub nodes
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        $this->subNodes   = $subNodes;
        $this->line       = $line;
        $this->docComment = $docComment;
        $this->attributes = array();
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() {
        return substr(get_class($this), 15);
    }

    /**
     * Gets the names of the sub nodes.
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames() {
        return array_keys($this->subNodes);
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Sets line the node started in.
     *
     * @param int $line Line
     */
    public function setLine($line) {
        $this->line = (int) $line;
    }

    /**
     * Gets the nearest doc comment.
     *
     * @return null|string Nearest doc comment or null
     */
    public function getDocComment() {
        return $this->docComment;
    }

    /**
     * Sets the nearest doc comment.
     *
     * @param null|string $docComment Nearest doc comment or null
     */
    public function setDocComment($docComment) {
        $this->docComment = $docComment;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($key) {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($key, $default = null) {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /* Magic interfaces */

    public function &__get($name) {
        return $this->subNodes[$name];
    }
    public function __set($name, $value) {
        $this->subNodes[$name] = $value;
    }
    public function __isset($name) {
        return isset($this->subNodes[$name]);
    }
    public function __unset($name) {
        unset($this->subNodes[$name]);
    }
    public function getIterator() {
        return new ArrayIterator($this->subNodes);
    }
}