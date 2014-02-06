<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

/**
 * @property StaticVar[] $vars Variable definitions
 */
class Static_ extends Stmt
{
    /**
     * Constructs a static variables list node.
     *
     * @param StaticVar[] $vars       Variable definitions
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(
            array(
                'vars' => $vars,
            ),
            $attributes
        );
    }
}