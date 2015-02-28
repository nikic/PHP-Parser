<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

/**
 * @property Expr $var  Variable reference is assigned to
 * @property Expr $expr Variable which is referenced
 */
class AssignRef extends Expr
{
    /** @var Expr Variable reference is assigned to */
    public $var;
    /** @var Expr Variable which is referenced */
    public $expr;

    /**
     * Constructs an assignment node.
     *
     * @param Expr  $var        Variable
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, Expr $expr, array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->var = $var;
        $this->expr = $expr;
    }

    public function getSubNodeNames() {
        return array('var', 'expr');
    }
}
