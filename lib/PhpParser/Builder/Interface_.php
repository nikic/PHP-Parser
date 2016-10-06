<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Interface_ extends Declaration
{
    protected $name;
    protected $extends = array();
    protected $constants = array();
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
     * Extends one or more interfaces.
     *
     * @param Name|string ...$interfaces Names of interfaces to extend
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function extend() {
        foreach (func_get_args() as $interface) {
            $this->extends[] = $this->normalizeName($interface);
        }

        return $this;
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

        $type = $statement->getType();
        switch ($type) {
            case 'Stmt_ClassConst':
                $this->constants[] = $statement;
                break;

            case 'Stmt_ClassMethod':
                // we erase all statements in the body of an interface method
                $statement->stmts = null;
                $this->methods[]  = $statement;
                break;

            default:
                throw new \LogicException(sprintf('Unexpected node of type "%s"', $type));
        }

        return $this;
    }

    /**
     * Returns the built interface node.
     *
     * @return Stmt\Interface_ The built interface node
     */
    public function getNode() {
        return new Stmt\Interface_($this->name, array(
            'extends' => $this->extends,
            'stmts' => array_merge($this->constants, $this->methods),
        ), $this->attributes);
    }
}