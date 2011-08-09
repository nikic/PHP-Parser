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
        if (!is_array($node) && !$node instanceof Traversable) {
            return;
        }

        $delNodes = array();
        $mrgNodes = array();

        foreach ($node as $subNodeKey => &$subNode) {
            if ($subNode instanceof PHPParser_NodeAbstract) {
                foreach ($this->visitors as $visitor) {
                    $visitor->enterNode($subNode);
                }
            }

            $this->_traverse($subNode);

            if ($subNode instanceof PHPParser_NodeAbstract) {
                foreach ($this->visitors as $visitor) {
                    $return = $visitor->leaveNode($subNode);

                    if (false === $return) {
                        $delNodes[] = $subNodeKey;
                    } elseif (is_array($return)) {
                        $mrgNodes[] = array($subNodeKey, $return);
                    }
                }
            }
        }

        if (!empty($delNodes)) {
            if (is_array($node)) {
                while ($delKey = array_pop($delNodes)) {
                    array_splice($node, $delKey, 1, array());
                }
            } else {
                while ($delKey = array_pop($delNodes)) {
                    unset($node[$delKey]);
                }
            }
        }

        if (!empty($mrgNodes)) {
            if (is_array($node)) {
                while (list($mrgKey, $mrgItems) = array_pop($mrgNodes)) {
                    array_splice($node, $mrgKey, 1, $mrgItems);
                }
            } else {
                throw new Exception('Nodes can only be merged if the parent is an array');
            }
        }
    }
}