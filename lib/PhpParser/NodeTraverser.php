<?php

namespace PhpParser;

class NodeTraverser implements NodeTraverserInterface
{
    /**
     * If NodeVisitor::enterNode() returns DONT_TRAVERSE_CHILDREN, child nodes
     * of the current node will not be traversed for any visitors.
     *
     * For subsequent visitors enterNode() will still be called on the current
     * node and leaveNode() will also be invoked for the current node.
     */
    const DONT_TRAVERSE_CHILDREN = 1;

    /**
     * If NodeVisitor::leaveNode() returns REMOVE_NODE for a node that occurs
     * in an array, it will be removed from the array.
     *
     * For subsequent visitors leaveNode() will still be invoked for the
     * removed node.
     */
    const REMOVE_NODE = false;

    /** @var NodeVisitor[] Visitors */
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
        foreach ($node->getSubNodeNames() as $name) {
            $subNode =& $node->$name;

            if (is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                $traverseChildren = true;
                foreach ($this->visitors as $visitor) {
                    $return = $visitor->enterNode($subNode);
                    if (self::DONT_TRAVERSE_CHILDREN === $return) {
                        $traverseChildren = false;
                    } else if (null !== $return) {
                        $subNode = $return;
                    }
                }

                if ($traverseChildren) {
                    $subNode = $this->traverseNode($subNode);
                }

                foreach ($this->visitors as $visitor) {
                    if (null !== $return = $visitor->leaveNode($subNode)) {
                        if (is_array($return)) {
                            throw new \LogicException(
                                'leaveNode() may only return an array ' .
                                'if the parent structure is an array'
                            );
                        }
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
                $traverseChildren = true;
                foreach ($this->visitors as $visitor) {
                    $return = $visitor->enterNode($node);
                    if (self::DONT_TRAVERSE_CHILDREN === $return) {
                        $traverseChildren = false;
                    } else if (null !== $return) {
                        $node = $return;
                    }
                }

                if ($traverseChildren) {
                    $node = $this->traverseNode($node);
                }

                foreach ($this->visitors as $visitor) {
                    $return = $visitor->leaveNode($node);

                    if (self::REMOVE_NODE === $return) {
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
