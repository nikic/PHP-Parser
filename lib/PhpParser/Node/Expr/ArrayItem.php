<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr      $value Value
 * @property null|Expr $key   Key
 * @property bool                     $byRef Whether to assign by reference
 */
class ArrayItem extends Expr
{
    /**
     * Constructs an array item node.
     *
     * @param Expr      $value      Value
     * @param null|Expr $key        Key
     * @param bool      $byRef      Whether to assign by reference
     * @param array     $attributes Additional attributes
     */
    public function __construct(Expr $value, Expr $key = null, $byRef = false, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
                'byRef' => $byRef
            ),
            $attributes
        );
    }
}