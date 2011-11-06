<?php

interface PHPParser_Node
{
    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType();

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine();

    /**
     * Sets line the node started in.
     *
     * @param int $line Line
     */
    public function setLine($line);

    /**
     * Gets the nearest doc comment.
     *
     * @return null|string Nearest doc comment or null
     */
    public function getDocComment();

    /**
     * Sets the nearest doc comment.
     *
     * @param null|string $docComment Nearest doc comment or null
     */
    public function setDocComment($docComment);
}