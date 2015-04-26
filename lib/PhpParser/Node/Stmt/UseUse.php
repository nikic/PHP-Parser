<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class UseUse extends Node\Stmt
{
    /** @var Node\Name Namespace, class, function or constant to alias */
    public $name;
    /** @var string Alias */
    public $alias;

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

        if ('self' == strtolower($alias) || 'parent' == strtolower($alias)) {
            throw new Error(sprintf(
                'Cannot use %s as %s because \'%2$s\' is a special class name',
                $name, $alias
            ));
        }

        parent::__construct(null, $attributes);
        $this->name = $name;
        $this->alias = $alias;
    }

    public function getSubNodeNames() {
        return array('name', 'alias');
    }
}
