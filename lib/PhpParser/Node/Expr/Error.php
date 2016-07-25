<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * Error node used during parsing with error recovery.
 *
 * An error node may be placed at a position where an expression is required, but an error occurred.
 * Error nodes will not be present if the parser is run in throwOnError mode (the default).
 */
class Error extends Expr
{
    /**
     * Constructs an error node.
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public function getSubNodeNames() {
        return array();
    }
}
