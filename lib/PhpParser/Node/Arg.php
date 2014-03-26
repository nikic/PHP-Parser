<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * @property Expr $value  Value to pass
 * @property bool $byRef  Whether to pass by ref
 * @property bool $unpack Whether to unpack the argument
 */
class Arg extends NodeAbstract
{
    /**
     * Constructs a function call argument node.
     *
     * @param Expr  $value      Value to pass
     * @param bool  $byRef      Whether to pass by ref
     * @param bool  $unpack     Whether to unpack the argument
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $value, $byRef = false, $unpack = false, array $attributes = array()) {
        parent::__construct(
            array(
                'value'  => $value,
                'byRef'  => $byRef,
                'unpack' => $unpack,
            ),
            $attributes
        );
    }
}