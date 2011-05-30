<?php

abstract class NodeAbstract implements IteratorAggregate
{
    protected $subNodes = array();

    public function __construct(array $subNodes) {
        $this->subNodes = $subNodes;
    }

    public function __get($name) {
        if (!array_key_exists($name, $this->subNodes)) {
            throw new OutOfBoundsException(
                sprintf('"%s" has no subnode "%s"', $this->getType(), $name)
            );
        }

        return $this->subNodes[$name];
    }

    public function getType() {
        return substr(get_class($this), 5);
    }

    public function getIterator() {
        return new ArrayIterator($this->subNodes);
    }
}