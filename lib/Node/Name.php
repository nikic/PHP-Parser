<?php

/**
 * @property array $parts Parts of the name
 */
class Node_Name extends NodeAbstract
{
    const ABSOLUTE = 1;
    const RELATIVE = 2;

    protected $resolveType;

    public function resolveType($type) {
        $this->resolveType = $type;
    }
}