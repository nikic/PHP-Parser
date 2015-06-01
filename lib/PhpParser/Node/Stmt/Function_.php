<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class Function_ extends Node\Stmt implements FunctionLike
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
    
    /**
     * {@inheritDoc}
     */
    public function returnsByRef() {
        return $this->byRef;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getReturnType() {
        return $this->returnType;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getStmts() {
        return $this->stmts;
    }
}
