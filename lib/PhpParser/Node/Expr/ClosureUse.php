<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ClosureUse extends Expr
{
    /** @var Expr\Variable Variable to use */
    public $var;
    /** @var bool Whether to use by reference */
    public $byRef;

    /**
     * Constructs a closure use node.
     *
     * @param Expr\Variable $var        Variable to use
     * @param bool          $byRef      Whether to use by reference
     * @param array         $attributes Additional attributes
     */
    public function __construct(Expr\Variable $var, bool $byRef = false, array $attributes = array()) {
        parent::__construct($attributes);
        $this->var = $var;
        $this->byRef = $byRef;
    }

    public function getSubNodeNames() : array {
        return array('var', 'byRef');
    }
}
