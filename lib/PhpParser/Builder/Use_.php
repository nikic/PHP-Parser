<?php

namespace PhpParser\Builder;

use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

/**
 * @method $this as(string $alias) Sets alias for used name.
 */
class Use_ implements Builder {
    protected $name;
    protected $type;
    protected $alias = null;

    /**
     * Creates a name use (alias) builder.
     *
     * @param Node\Name|string $name Name of the entity (namespace, class, function, constant) to alias
     * @param int              $type One of the Stmt\Use_::TYPE_* constants
     */
    public function __construct($name, int $type) {
        $this->name = BuilderHelpers::normalizeName($name);
        $this->type = $type;
    }

    /**
     * Sets alias for used name.
     *
     * @param string $alias Alias to use (last component of full name by default)
     *
     * @return $this The builder instance (for fluid interface)
     */
    protected function as_(string $alias) {
        $this->alias = $alias;
        return $this;
    }
    public function __call($name, $args) {
        if (method_exists($this, $name . '_')) {
            return $this->{$name . '_'}(...$args);
        }

        throw new \LogicException(sprintf('Method "%s" does not exist', $name));
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : Node {
        return new Stmt\Use_(array(
            new Stmt\UseUse($this->name, $this->alias)
        ), $this->type);
    }
}
