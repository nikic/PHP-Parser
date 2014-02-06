<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Expr            $cond    Condition expression
 * @property Node[]               $stmts   Statements
 * @property Node\Stmt\ElseIf_[]  $elseifs Elseif clauses
 * @property null|Node\Stmt\Else_ $else    Else clause
 */
class If_ extends Node\Stmt
{

    /**
     * Constructs an if node.
     *
     * @param Node\Expr $cond       Condition
     * @param array     $subNodes   Array of the following optional subnodes:
     *                              'stmts'   => array(): Statements
     *                              'elseifs' => array(): Elseif clauses
     *                              'else'    => null   : Else clause
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'stmts'   => array(),
                'elseifs' => array(),
                'else'    => null,
            ),
            $attributes
        );
        $this->cond = $cond;
    }
}