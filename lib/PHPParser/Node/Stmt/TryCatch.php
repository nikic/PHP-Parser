<?php

/**
 * @property PHPParser_Node[]            $stmts   Statements
 * @property PHPParser_Node_Stmt_Catch[] $catches Catches
 */
class PHPParser_Node_Stmt_TryCatch extends PHPParser_Node_Stmt
{
    /**
     * Constructs a try catch node.
     *
     * @param PHPParser_Node[]            $stmts      Statements
     * @param PHPParser_Node_Stmt_Catch[] $catches    Catches
     * @param array                       $attributes Additional attributes
     */
    public function __construct(array $stmts, array $catches, array $attributes = array()) {
        parent::__construct(
            array(
                'stmts'   => $stmts,
                'catches' => $catches,
            ),
            $attributes
        );
    }
}