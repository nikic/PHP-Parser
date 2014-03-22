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
     *                          'static' => false  : Whether the closure is static
     *                          'byRef'  => false  : Whether to return by reference
     *                          'params' => array(): Parameters
     *                          'uses'   => array(): use()s
     *                          'stmts'  => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'static' => isset($subNodes['static']) ? $subNodes['static'] : false,
                'byRef'  => isset($subNodes['byRef'])  ? $subNodes['byRef']  : false,
                'params' => isset($subNodes['params']) ? $subNodes['params'] : array(),
                'uses'   => isset($subNodes['uses'])   ? $subNodes['uses']   : array(),
                'stmts'  => isset($subNodes['stmts'])  ? $subNodes['stmts']  : array(),
            ),
            $attributes
        );
    }
}