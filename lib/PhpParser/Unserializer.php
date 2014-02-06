<?php

namespace PhpParser;

interface Unserializer
{
    /**
     * Unserializes a string in some format into a node tree.
     *
     * @param string $string Serialized string
     *
     * @return array Statements
     */
    public function unserialize($string);
}
