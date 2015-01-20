<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property string $name  Name
 * @property Node[] $stmts Statements
 */
class Trait_ extends Node\Stmt
{
    /**
     * Constructs a trait node.
     *
     * @param string $name       Name
     * @param Node[] $stmts      Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }

    public function getMethods() {
        $methods = array();
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $methods[] = $stmt;
            }
        }
        return $methods;
    }
}
