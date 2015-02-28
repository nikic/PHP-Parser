<?php

namespace PhpParser;

abstract class NodeAbstract implements Node
{
    private $subNodeNames;
    protected $attributes;

    /**
     * Creates a Node.
     *
     * If null is passed for the $subNodes parameter the node constructor must assign
     * all subnodes by itself and also override the getSubNodeNames() method.
     * DEPRECATED: If an array is passed as $subNodes instead, the properties corresponding
     * to the array keys will be set and getSubNodeNames() will return the keys of that
     * array.
     *
     * @param null|array $subNodes   Null or an array of sub nodes (deprecated)
     * @param array      $attributes Array of attributes
     */
    public function __construct($subNodes = array(), array $attributes = array()) {
        $this->attributes = $attributes;

        if (null !== $subNodes) {
            foreach ($subNodes as $name => $value) {
                $this->$name = $value;
            }
            $this->subNodeNames = array_keys($subNodes);
        }
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() {
        return strtr(substr(rtrim(get_class($this), '_'), 15), '\\', '_');
    }

    /**
     * Gets the names of the sub nodes.
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames() {
        return $this->subNodeNames;
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine() {
        return $this->getAttribute('startLine', -1);
    }

    /**
     * Sets line the node started in.
     *
     * @param int $line Line
     */
    public function setLine($line) {
        $this->setAttribute('startLine', (int) $line);
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|Comment\Doc Doc comment object or null
     */
    public function getDocComment() {
        $comments = $this->getAttribute('comments');
        if (!$comments) {
            return null;
        }

        $lastComment = $comments[count($comments) - 1];
        if (!$lastComment instanceof Comment\Doc) {
            return null;
        }

        return $lastComment;
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
    public function &getAttribute($key, $default = null) {
        if (!array_key_exists($key, $this->attributes)) {
            return $default;
        } else {
            return $this->attributes[$key];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes() {
        return $this->attributes;
    }
}
