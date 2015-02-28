<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class New_ extends Expr
{
    /** @var Node\Name|Expr Class name */
    public $class;
    /** @var Node\Arg[] Arguments */
    public $args;

    /**
     * Constructs a function call node.
     *
     * @param Node\Name|Expr $class      Class name
     * @param Node\Arg[]     $args       Arguments
     * @param array          $attributes Additional attributes
     */
    public function __construct($class, array $args = array(), array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->class = $class;
        $this->args = $args;
    }

    public function getSubNodeNames() {
        return array('class', 'args');
    }
}
