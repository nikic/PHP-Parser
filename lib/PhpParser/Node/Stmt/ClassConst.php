<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property Node\Const_[] $consts Constant declarations
 */
class ClassConst extends Node\Stmt
{
    /**
     * Constructs a class const list node.
     *
     * @param Node\Const_[] $consts     Constant declarations
     * @param array         $attributes Additional attributes
     */
    public function __construct(array $consts, array $attributes = array()) {
        parent::__construct(
            array(
                'consts' => $consts,
            ),
            $attributes
        );
    }
}