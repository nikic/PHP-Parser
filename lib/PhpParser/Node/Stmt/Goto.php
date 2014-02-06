<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

/**
 * @property string $name Name of label to jump to
 */
class Goto_ extends Stmt
{
    /**
     * Constructs a goto node.
     *
     * @param string $name       Name of label to jump to
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct(
            array(
                'name' => $name,
            ),
            $attributes
        );
    }
}