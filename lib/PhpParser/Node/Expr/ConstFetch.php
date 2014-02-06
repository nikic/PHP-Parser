<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Name;
use PhpParser\Node\Expr;

/**
 * @property Name $name Constant name
 */
class ConstFetch extends Expr
{
    /**
     * Constructs a const fetch node.
     *
     * @param Name  $name       Constant name
     * @param array $attributes Additional attributes
     */
    public function __construct(Name $name, array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name
            ),
            $attributes
        );
    }
}