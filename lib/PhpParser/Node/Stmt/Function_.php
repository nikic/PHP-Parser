<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Function_ extends Node\Stmt
{
    /** @var bool Whether function returns by reference */
    public $byRef;
    /** @var string Name */
    public $name;
    /** @var Node\Param[] Parameters */
    public $params;
    /** @var null|string|Node\Name[] Return type */
    public $returnType;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a function node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'byRef'      => false  : Whether to return by reference
     *                           'params'     => array(): Parameters
     *                           'returnType' => null   : Return type
     *                           'stmts'      => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->byRef = isset($subNodes['byRef']) ? $subNodes['byRef'] : false;
        $this->name = $name;
        $this->params = isset($subNodes['params']) ? $subNodes['params'] : array();
        $this->returnType = isset($subNodes['returnType']) ? $subNodes['returnType'] : null;
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();
    }

    public function getSubNodeNames() {
        return array('byRef', 'name', 'params', 'returnType', 'stmts');
    }
}
