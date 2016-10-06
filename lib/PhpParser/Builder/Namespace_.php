<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Namespace_ extends PhpParser\BuilderAbstract
{
    private $name;
    private $statements = array();

    /**
     * Creates a namespace builder.
     *
     * @param Node\Name|string|null $name Name of the namespace
     */
    public function __construct($name) {
        $this->name = null !== $name ? $this->normalizeName($name) : null;
    }

    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $statement The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStatement($statement) {
        $this->statements[] = $this->normalizeNode($statement);

        return $this;
    }

    /**
     * Adds multiple statements.
     *
     * @param array $statements The statements to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStatements(array $statements) {
        foreach ($statements as $stmt) {
            $this->addStatement($stmt);
        }

        return $this;
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() {
        return new Stmt\Namespace_($this->name, $this->statements);
    }
}
