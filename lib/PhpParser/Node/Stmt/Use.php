<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

/**
 * @property UseUse[] $uses Aliases
 */
class Use_ extends Stmt
{
    /**
     * Constructs an alias (use) list node.
     *
     * @param UseUse[] $uses       Aliases
     * @param array    $attributes Additional attributes
     */
    public function __construct(array $uses, array $attributes = array()) {
        parent::__construct(
            array(
                'uses' => $uses,
            ),
            $attributes
        );
    }
}