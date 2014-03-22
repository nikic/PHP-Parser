<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Expr      $expr     Expression to iterate
 * @property null|Node\Expr $keyVar   Variable to assign key to
 * @property bool                     $byRef    Whether to assign value by reference
 * @property Node\Expr      $valueVar Variable to assign value to
 * @property Node[]         $stmts    Statements
 */
class Foreach_ extends Node\Stmt
{
    /**
     * Constructs a foreach node.
     *
     * @param Node\Expr $expr       Expression to iterate
     * @param Node\Expr $valueVar   Variable to assign value to
     * @param array     $subNodes   Array of the following optional subnodes:
     *                              'keyVar' => null   : Variable to assign key to
     *                              'byRef'  => false  : Whether to assign value by reference
     *                              'stmts'  => array(): Statements
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, Node\Expr $valueVar, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'expr'     => $expr,
                'keyVar'   => isset($subNodes['keyVar']) ? $subNodes['keyVar'] : null,
                'byRef'    => isset($subNodes['byRef'])  ? $subNodes['byRef']  : false,
                'valueVar' => $valueVar,
                'stmts'    => isset($subNodes['stmts'])  ? $subNodes['stmts']  : array(),
            ),
            $attributes
        );
    }
}