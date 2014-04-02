<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property bool         $byRef  Whether returns by reference
 * @property string       $name   Name
 * @property Node\Param[] $params Parameters
 * @property Node[]       $stmts  Statements
 */
class Function_ extends Node\Stmt
{
    /**
     * Constructs a function node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'byRef'  => false  : Whether to return by reference
     *                           'params' => array(): Parameters
     *                           'stmts'  => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'byRef'  => isset($subNodes['byRef'])  ? $subNodes['byRef']  : false,
                'name'   => $name,
                'params' => isset($subNodes['params']) ? $subNodes['params'] : array(),
                'stmts'  => isset($subNodes['stmts'])  ? $subNodes['stmts']  : array(),
            ),
            $attributes
        );
        $this->name = $name;
    }
}