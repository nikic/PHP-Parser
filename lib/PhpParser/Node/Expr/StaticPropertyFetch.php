<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\VarLikeIdentifier;

class StaticPropertyFetch extends Expr
{
    /** @var Name|Expr Class name */
    public $class;
    /** @var VarLikeIdentifier|Expr Property name */
    public $name;

    /**
     * Constructs a static property fetch node.
     *
     * @param Name|Expr                     $class      Class name
     * @param string|VarLikeIdentifier|Expr $name       Property name
     * @param array                         $attributes Additional attributes
     */
    public function __construct($class, $name, array $attributes = array()) {
        parent::__construct($attributes);
        $this->class = $class;
        $this->name = \is_string($name) ? new VarLikeIdentifier($name) : $name;
    }

    public function getSubNodeNames() : array {
        return array('class', 'name');
    }
}
