<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * Class, interface or trait.
 *
 * @property string $name  Name
 * @property Node[] $stmts Statements
 */
abstract class ClassLike extends Node\Stmt {
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
