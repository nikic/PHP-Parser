<?php

namespace PhpParser;

use PhpParser\Node;

abstract class NodeAbstract implements Node, \JsonSerializable
{
    protected $attributes;

    /**
     * Creates a Node.
     *
     * @param array $attributes Array of attributes
     */
    public function __construct(array $attributes = array()) {
        $this->attributes = $attributes;
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() {
        $className = rtrim(get_class($this), '_');
        return strtr(
            substr($className, strlen(Node::class) + 1),
            '\\',
            '_'
        );
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
     *
     * @deprecated
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
     * Sets the doc comment of the node.
     *
     * This will either replace an existing doc comment or add it to the comments array.
     *
     * @param Comment\Doc $docComment Doc comment to set
     */
    public function setDocComment(Comment\Doc $docComment) {
        $comments = $this->getAttribute('comments', []);

        $numComments = count($comments);
        if ($numComments > 0 && $comments[$numComments - 1] instanceof Comment\Doc) {
            // Replace existing doc comment
            $comments[$numComments - 1] = $docComment;
        } else {
            // Append new comment
            $comments[] = $docComment;
        }

        $this->setAttribute('comments', $comments);
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function hasAttribute($key) {
        return array_key_exists($key, $this->attributes);
    }

    public function &getAttribute($key, $default = null) {
        if (!array_key_exists($key, $this->attributes)) {
            return $default;
        } else {
            return $this->attributes[$key];
        }
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function jsonSerialize() {
        return ['nodeType' => $this->getType()] + get_object_vars($this);
    }
}
