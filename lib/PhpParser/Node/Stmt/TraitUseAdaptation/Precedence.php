<?php

namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

/**
 * @property Node\Name   $trait     Trait name
 * @property string      $method    Method name
 * @property Node\Name[] $insteadof Overwritten traits
 */
class Precedence extends Node\Stmt\TraitUseAdaptation
{
    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param Node\Name   $trait       Trait name
     * @param string      $method      Method name
     * @param Node\Name[] $insteadof   Overwritten traits
     * @param array       $attributes  Additional attributes
     */
    public function __construct(Node\Name $trait, $method, array $insteadof, array $attributes = array()) {
        parent::__construct(
            array(
                'trait'     => $trait,
                'method'    => $method,
                'insteadof' => $insteadof,
            ),
            $attributes
        );
    }
}