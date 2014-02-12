<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property Node\Name $name  Namespace/Class to alias
 * @property string    $alias Alias
 */
class UseUse extends Node\Stmt
{
    /**
     * Constructs an alias (use) node.
     *
     * @param Node\Name   $name       Namespace/Class to alias
     * @param null|string $alias      Alias
     * @param array       $attributes Additional attributes
     */
    public function __construct(Node\Name $name, $alias = null, array $attributes = array()) {
        if (null === $alias) {
            $alias = $name->getLast();
        }

        if ('self' == $alias || 'parent' == $alias) {
            throw new Error(sprintf(
                'Cannot use %s as %s because \'%2$s\' is a special class name',
                $name, $alias
            ));
        }

        parent::__construct(
            array(
                'name'  => $name,
                'alias' => $alias,
            ),
            $attributes
        );
    }
}
