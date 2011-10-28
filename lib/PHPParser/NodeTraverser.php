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

        $nodes = $this->traverseArray($nodes);

        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->afterTraverse($nodes)) {
                $nodes = $return;
            }
        }

        return $nodes;
    }

    protected function traverseNode(PHPParser_Node $node) {
        foreach ($node as $name => $subNode) {
            if (is_array($subNode)) {
                $node->$name = $this->traverseArray($subNode);
            } elseif ($subNode instanceof PHPParser_Node) {
                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->enterNode($subNode)) {
                        $node->$name = $return;
                    }
                }

                $node->$name = $this->traverseNode($subNode);

                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->leaveNode($subNode)) {
                        $node->$name = $return;
                    }
                }
            }
        }

        return $node;
    }

    protected function traverseArray(array $nodes) {
        $doNodes = array();

        foreach ($nodes as $i => $node) {
            if (is_array($node)) {
                $nodes[$i] = $this->traverseArray($node);
            } elseif ($node instanceof PHPParser_Node) {
                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->enterNode($node)) {
                        $nodes[$i] = $return;
                    }
                }

                $nodes[$i] = $this->traverseNode($node);

                foreach ($this->visitors as $j => $visitor) {
                    $return = $visitor->leaveNode($node);

                    if (false === $return) {
                        $doNodes[] = array($i, array());
                        break;
                    } elseif (is_array($return)) {
                        // traverse replacement nodes using all visitors apart from the one that
                        // did the change
                        unset($this->visitors[$j]);
                        $return = $this->traverseArray($return);
                        $this->visitors[$j] = $visitor;

                        $doNodes[] = array($i, $return);
                        break;
                    } elseif (null !== $return) {
                        $nodes[$i] = $return;
                    }
                }
            }
        }

        if (!empty($doNodes)) {
            while (list($i, $replace) = array_pop($doNodes)) {
                array_splice($nodes, $i, 1, $replace);
            }
        }

        return $nodes;
    }
}