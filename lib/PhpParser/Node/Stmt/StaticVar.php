<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property string         $name    Name
 * @property null|Node\Expr $default Default value
 */
class StaticVar extends Node\Stmt
{
    /**
     * Constructs a static variable node.
     *
     * @param string         $name       Name
     * @param null|Node\Expr $default    Default value
     * @param array          $attributes Additional attributes
     */
    public function __construct($name, Node\Expr $default = null, array $attributes = array()) {
        parent::__construct(
            array(
                'name'    => $name,
                'default' => $default,
            ),
            $attributes
        );
    }
}