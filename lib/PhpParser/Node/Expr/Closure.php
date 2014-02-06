<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

/**
 * @property Node[]       $stmts  Statements
 * @property Node\Param[] $params Parameters
 * @property ClosureUse[] $uses   use()s
 * @property bool         $byRef  Whether to return by reference
 * @property bool         $static Whether the closure is static
 */
class Closure extends Expr
{
    /**
     * Constructs a lambda function node.
     *
     * @param array $subNodes   Array of the following optional subnodes:
     *                          'stmts'  => array(): Statements
     *                          'params' => array(): Parameters
     *                          'uses'   => array(): use()s
     *                          'byRef'  => false  : Whether to return by reference
     *                          'static' => false  : Whether the closure is static
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'stmts'  => array(),
                'params' => array(),
                'uses'   => array(),
                'byRef'  => false,
                'static' => false,
            ),
            $attributes
        );
    }
}