<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Trait_ extends Declaration
{
    protected $name;
    protected $properties = array();
    protected $methods = array();

    /**
     * Creates an interface builder.
     *
     * @param string $name Name of the interface
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Adds a statement.
     *
     * @param Stmt|PhpParser\Builder $statement The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStatement($statement) {
        $statement = $this->normalizeNode($statement);

        if ($statement instanceof Stmt\Property) {
            $this->properties[] = $statement;
        } else if ($statement instanceof Stmt\ClassMethod) {
            $this->methods[] = $statement;
        } else {
            throw new \LogicException(sprintf('Unexpected node of type "%s"', $statement->getType()));
        }

        return $this;
    }

    /**
     * Returns the built trait node.
     *
     * @return Stmt\Trait_ The built interface node
     */
    public function getNode() {
        return new Stmt\Trait_(
            $this->name, array(
                'stmts' => array_merge($this->properties, $this->methods)
            ), $this->attributes
        );
    }
}
