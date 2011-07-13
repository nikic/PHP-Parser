<?php

class PHPParser_NodeTraverser
{
    const ON_ENTER = 1;
    const ON_LEAVE = 2;

    protected $enterVisitors;
    protected $leaveVisitors;

    /**
     * Constructs a node traverser.
     */
    public function __construct() {
        $this->enterVisitors = $this->leaveVisitors = array();
    }

    /**
     * Adds a node visitor.
     *
     * The callback gets the node passed as only argument and may return a replacement for the node
     * or return null/nothing, in which case the node is left untouched.
     *
     * The mode specifies when the node should be visited:
     *  * using self::ON_ENTER the node is visited before traversing its sub nodes
     *  * using self::ON_LEAVE the node is visited after traversing its sub nodes
     *  * using self::ON_ENTER | self::ON_LEAVE the node is visited both before and after traversing
     *    its subnodes
     * The default is to visit the node ON_ENTER.
     *
     * Visitors are called in the order they were registered in.
     *
     * @param callback $visitor Visitor callback
     * @param int      $mode    Visitor mode (bitmap using self::ON_ENTER and self::ON_LEAVE)
     *
     * @throws InvalidArgumentException if visitor is not callable
     */
    public function addVisitor($visitor, $mode = self::ON_ENTER) {
        if (!is_callable($visitor)) {
            throw new InvalidArgumentException('Visitor not callable');
        }

        if ($mode & self::ON_ENTER) {
            $this->enterVisitors[] = $visitor;
        }

        if ($mode & self::ON_LEAVE) {
            $this->leaveVisitors[] = $visitor;
        }
    }

    /**
     * Traverses a node or an array using the registered visitors.
     *
     * @param PHPParser_NodeAbstract|array $node Node or array
     *
     * @return mixed Node after all visitors were applied.
     */
    public function traverse($node) {
        if ($node instanceof PHPParser_NodeAbstract) {
            foreach ($this->enterVisitors as $visitor) {
                if (null !== $return = call_user_func($visitor, $node)) {
                    $node = $return;
                }
            }
        }

        if (is_array($node) || $node instanceof Traversable) {
            foreach ($node as &$subNode) {
                $subNode = $this->traverse($subNode);
            }
        }

        if ($node instanceof PHPParser_NodeAbstract) {
            foreach ($this->leaveVisitors as $visitor) {
                if (null !== $return = call_user_func($visitor, $node)) {
                    $node = $return;
                }
            }
        }

        return $node;
    }
}