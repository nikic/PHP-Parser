<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

/**
 * @property string $value String
 */
class InlineHTML extends Stmt
{
    /**
     * Constructs an inline HTML node.
     *
     * @param string $value      String
     * @param array  $attributes Additional attributes
     */
    public function __construct($value, array $attributes = array()) {
        parent::__construct(
            array(
                'value' => $value,
            ),
            $attributes
        );
    }
}