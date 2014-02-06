<?php

namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

/**
 * @property null|Node\Name $trait       Trait name
 * @property string         $method      Method name
 * @property null|int       $newModifier New modifier
 * @property null|string    $newName     New name
 */
class Alias extends Node\Stmt\TraitUseAdaptation
{
    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param null|Node\Name $trait       Trait name
     * @param string         $method      Method name
     * @param null|int       $newModifier New modifier
     * @param null|string    $newName     New name
     * @param array          $attributes  Additional attributes
     */
    public function __construct($trait, $method, $newModifier, $newName, array $attributes = array()) {
        parent::__construct(
            array(
                'trait'       => $trait,
                'method'      => $method,
                'newModifier' => $newModifier,
                'newName'     => $newName,
            ),
            $attributes
        );
    }
}