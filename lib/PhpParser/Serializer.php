<?php

namespace PhpParser;

/**
 * @deprecated
 */
interface Serializer
{
    /**
     * Serializes statements into some string format.
     *
     * @param array $nodes Statements
     *
     * @return string Serialized string
     */
    public function serialize(array $nodes);
}