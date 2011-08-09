<?php

class PHPParser_NodeTraverser
{
    /**
     * @var PHPParser_NodeVisitorInterface[] Visitors
     */
    protected $visitors;

    /**
     * Constructs a node traverser.
     */
    public function __construct() {
        $this->visitors = array();
    }

    /**
     * Adds a visitor.
     *
     * @param PHPParser_NodeVisitorInterface $visitor Visitor to add
     */
    public function addVisitor(PHPParser_NodeVisitorInterface $visitor) {
        $this->visitors[] = $visitor;
    }

    /**
     * Traverses a node or an array using the registered visitors.
     *
     * @param PHPParser_NodeAbstract|array $node Node or array
     */
    public function traverse(&$node) {
        foreach ($this->visitors as $visitor) {
            $visitor->beforeTraverse($node);
        }

        $this->_traverse($node);

        foreach ($this->visitors as $visitor) {
            $visitor->afterTraverse($node);
        }
    }

    protected function _traverse(&$node) {
        if ($node instanceof PHPParser_NodeAbstract) {
            foreach ($this->visitors as $visitor) {
                $visitor->enterNode($node);
            }
        }

        if (is_array($node) || $node instanceof Traversable) {
            foreach ($node as &$subNode) {
                $this->_traverse($subNode);
            }
        }

        if ($node instanceof PHPParser_NodeAbstract) {
            foreach ($this->visitors as $visitor) {
                $visitor->leaveNode($node);
            }
        }
    }
}