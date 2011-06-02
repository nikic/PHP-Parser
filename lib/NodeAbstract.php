<?php

abstract class NodeAbstract implements IteratorAggregate
{
    protected $subNodes = array();

    /**
     * Creates a Node.
     *
     * @param array $subNodes Array of sub nodes
     */
    public function __construct(array $subNodes) {
        $this->subNodes = $subNodes;
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
     * Node_ prefix.
     *
     * @return string Type of this node
     */
    public function getType() {
        return substr(get_class($this), 5);
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