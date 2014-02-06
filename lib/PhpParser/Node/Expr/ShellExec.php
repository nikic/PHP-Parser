<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property array $parts Encapsed string array
 */
class ShellExec extends Expr
{
    /**
     * Constructs a shell exec (backtick) node.
     *
     * @param array $parts      Encapsed string array
     * @param array $attributes Additional attributes
     */
    public function __construct($parts, array $attributes = array()) {
        parent::__construct(
            array(
                'parts' => $parts
            ),
            $attributes
        );
    }
}