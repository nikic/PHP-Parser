<?php

class PHPParser_NodeTraverser
{
    /**
     * @var PHPParser_NodeVisitor[] Visitors
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
     * @param PHPParser_NodeVisitor $visitor Visitor to add
     */
    public function addVisitor(PHPParser_NodeVisitor $visitor) {
        $this->visitors[] = $visitor;
    }

    /**
     * Traverses an array of nodes using the registered visitors.
     *
     * @param PHPParser_Node[] $nodes Array of nodes
     *
     * @return PHPParser_Node[] Traversed array of nodes
     */
    public function traverse(array $nodes) {
        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->beforeTraverse($nodes)) {
                $nodes = $return;
            }
        }

        $nodes = $this->_traverse($nodes);

        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->afterTraverse($nodes)) {
                $nodes = $return;
            }
        }

        return $nodes;
    }

    protected function _traverse($node) {
        $doNodes = array();

        foreach ($node as $name => $subNode) {
            if (is_array($subNode)) {
                $node[$name] = $this->_traverse($subNode, $this->visitors);
            } elseif ($subNode instanceof PHPParser_Node) {
                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->enterNode($subNode)) {
                        $node[$name] = $return;
                    }
                }

                $node[$name] = $this->_traverse($subNode, $this->visitors);

                foreach ($this->visitors as $i => $visitor) {
                    $return = $visitor->leaveNode($subNode);

                    if (false === $return) {
                        $doNodes[] = array($name, array());
                        break;
                    } elseif (is_array($return)) {
                        // traverse replacement nodes using all visitors apart from the one that
                        // did the change
                        unset($this->visitors[$i]);
                        $return = $this->_traverse($return);
                        $this->visitors[$i] = $visitor;

                        $doNodes[] = array($name, $return);
                        break;
                    } elseif (null !== $return) {
                        $node[$name] = $return;
                    }
                }
            }
        }

        if (!empty($doNodes)) {
            if (!is_array($node)) {
                throw new Exception('Nodes can only be merged if the parent is an array');
            }

            while (list($key, $replace) = array_pop($doNodes)) {
                array_splice($node, $key, 1, $replace);
            }
        }

        return $node;
    }
}