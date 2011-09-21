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
     * Gets the nearest doc comment.
     *
     * @return null|string Nearest doc comment or null
     */
    public function getDocComment();
}