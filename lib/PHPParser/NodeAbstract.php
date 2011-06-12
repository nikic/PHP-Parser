<?php

abstract class PHPParser_NodeAbstract implements IteratorAggregate
{
    protected $subNodes;
    protected $line;

    /**
     * Creates a Node.
     *
     * @param array $subNodes Array of sub nodes
     * @param int   $line     Line
     */
    public function __construct(array $subNodes, $line = -1) {
        $this->subNodes = $subNodes;
        $this->line     = $line;
    }

    /**
     * Gets a sub node.
     *
     * @param string $name Name of sub node
     *
     * @return mixed Sub node
     */
    public function __get($name) {
        if (!array_key_exists($name, $this->subNodes)) {
            throw new InvalidArgumentException(
                sprintf('"%s" has no subnode "%s"', $this->getType(), $name)
            );
        }

        return $this->subNodes[$name];
    }

    /**
     * Sets a sub node.
     *
     * @param string $name  Name of sub node
     * @param mixed  $value Value to set sub node to
     */
    public function __set($name, $value) {
        $this->subNodes[$name] = $value;
    }

    /**
     * Checks whether a subnode exists.
     *
     * @param string $name Name of sub node
     *
     * @return bool Whether the sub node exists
     */
    public function __isset($name) {
        return isset($this->subNodes[$name]);
    }

    /**
     * Gets the type of this node.
     *
     * The type of a node is the node's class name without the
     * PHPParser_Node_ prefix.
     *
     * @return string Type of this node
     */
    public function getType() {
        return substr(get_class($this), 15);
    }

    /**
     * Gets line the node *ended* in.
     *
     * TODO: We probably want the line it started in...
     *
     * @return int Line
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Gets an Iterator for the sub nodes.
     *
     * @return ArrayIterator Iterator for sub nodes
     */
    public function getIterator() {
        return new ArrayIterator($this->subNodes);
    }
}