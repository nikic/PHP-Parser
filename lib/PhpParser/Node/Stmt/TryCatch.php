<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property Node[]   $stmts        Statements
 * @property Catch_[] $catches      Catches
 * @property Node[]   $finallyStmts Finally statements
 */
class TryCatch extends Node\Stmt
{
    /**
     * Constructs a try catch node.
     *
     * @param Node[]     $stmts        Statements
     * @param Catch_[]   $catches      Catches
     * @param Node[]     $finallyStmts Finally statements (null means no finally clause)
     * @param array|null $attributes   Additional attributes
     */
    public function __construct(array $stmts, array $catches, array $finallyStmts = null, array $attributes = array()) {
        if (empty($catches) && null === $finallyStmts) {
            throw new Error('Cannot use try without catch or finally');
        }

        parent::__construct(
            array(
                'stmts'        => $stmts,
                'catches'      => $catches,
                'finallyStmts' => $finallyStmts,
            ),
            $attributes
        );
    }
}