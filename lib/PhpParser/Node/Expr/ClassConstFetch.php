<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class ClassConstFetch extends Expr
{
    /** @var Name|Expr Class name */
    public $class;
    /** @var string|Error Constant name */
    public $name;

    /**
     * Constructs a class const fetch node.
     *
     * @param Name|Expr    $class      Class name
     * @param string|Error $name       Constant name
     * @param array        $attributes Additional attributes
     */
    public function __construct($class, $name, array $attributes = array()) {
        parent::__construct($attributes);
        $this->class = $class;
        $this->name = $name;
    }

    public function getSubNodeNames() {
        return array('class', 'name');
    }
}
