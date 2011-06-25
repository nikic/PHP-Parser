<?php

abstract class PHPParser_NodeAbstract extends ArrayObject
{
    protected $line;

    /**
     * Creates a Node.
     *
     * @param array $subNodes Array of sub nodes
     * @param int   $line     Line
     */
    public function __construct(array $subNodes, $line = -1) {
        parent::__construct($subNodes, ArrayObject::ARRAY_AS_PROPS);

        $this->line = $line;
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
}