<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Isset_ extends Expr
{
    /** @var Expr[] Variables */
    public $vars;

    /**
     * Constructs an array node.
     *
     * @param Expr[] $vars       Variables
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->vars = $vars;
    }

    public function getSubNodeNames() {
        return array('vars');
    }
}
