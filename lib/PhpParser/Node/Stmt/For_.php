<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class For_ extends Node\Stmt
{
    /** @var Node\Expr[] Init expressions */
    public $init;
    /** @var Node\Expr[] Loop conditions */
    public $cond;
    /** @var Node\Expr[] Loop expressions */
    public $loop;
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a for loop node.
     *
     * @param array $subNodes   Array of the following optional subnodes:
     *                          'init'  => array(): Init expressions
     *                          'cond'  => array(): Loop conditions
     *                          'loop'  => array(): Loop expressions
     *                          'stmts' => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->init = $subNodes['init'] ?? array();
        $this->cond = $subNodes['cond'] ?? array();
        $this->loop = $subNodes['loop'] ?? array();
        $this->stmts = $subNodes['stmts'] ?? array();
    }

    public function getSubNodeNames() : array {
        return array('init', 'cond', 'loop', 'stmts');
    }
}
