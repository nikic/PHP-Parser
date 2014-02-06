<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

/**
 * @property string $remaining Remaining text after halt compiler statement.
 */
class HaltCompiler extends Stmt
{
    /**
     * Constructs a __halt_compiler node.
     *
     * @param string $remaining  Remaining text after halt compiler statement.
     * @param array  $attributes Additional attributes
     */
    public function __construct($remaining, array $attributes = array()) {
        parent::__construct(
            array(
                'remaining' => $remaining,
            ),
            $attributes
        );
    }
}