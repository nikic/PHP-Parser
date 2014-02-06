<?php

namespace PhpParser;

class NodeTraverser implements NodeTraverserInterface
{
    /**
     * @var NodeVisitor[] Visitors
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
     * @param NodeVisitor $visitor Visitor to add
     */
    public function addVisitor(NodeVisitor $visitor) {
        $this->visitors[] = $visitor;
    }

    /**
     * Removes an added visitor.
     *
     * @param NodeVisitor $visitor
     */
    public function removeVisitor(NodeVisitor $visitor) {
        foreach ($this->visitors as $index => $storedVisitor) {
            if ($storedVisitor === $visitor) {
                unset($this->visitors[$index]);
                break;
            }
        }
    }

    /**
     * Traverses an array of nodes using the registered visitors.
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return Node[] Traversed array of nodes
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

    protected function traverseNode(Node $node) {
        $node = clone $node;

        foreach ($node->getSubNodeNames() as $name) {
            $subNode =& $node->$name;

            if (is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->enterNode($subNode)) {
                        $subNode = $return;
                    }
                }

                $subNode = $this->traverseNode($subNode);

                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->leaveNode($subNode)) {
                        $subNode = $return;
                    }
                }
            }
        }

        return $node;
    }

    protected function traverseArray(array $nodes) {
        $doNodes = array();

        foreach ($nodes as $i => &$node) {
            if (is_array($node)) {
                $node = $this->traverseArray($node);
            } elseif ($node instanceof Node) {
                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->enterNode($node)) {
                        $node = $return;
                    }
                }

                $node = $this->traverseNode($node);

                foreach ($this->visitors as $visitor) {
                    $return = $visitor->leaveNode($node);

                    if (false === $return) {
                        $doNodes[] = array($i, array());
                        break;
                    } elseif (is_array($return)) {
                        $doNodes[] = array($i, $return);
                        break;
                    } elseif (null !== $return) {
                        $node = $return;
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