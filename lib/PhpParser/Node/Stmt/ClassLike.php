<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

abstract class ClassLike extends Node\Stmt {
    /** @var string Name */
    public $name;
    /** @var Node[] Statements */
    public $stmts;

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
