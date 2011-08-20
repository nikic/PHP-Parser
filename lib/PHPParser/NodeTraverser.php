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

        $this->_traverse($node, $this->visitors);

        foreach ($this->visitors as $visitor) {
            $visitor->afterTraverse($node);
        }
    }

    protected function _traverse(&$node, array $visitors) {
        if (!is_array($node) && !$node instanceof Traversable) {
            return;
        }

        $doNodes = array();

        foreach ($node as $subNodeKey => &$subNode) {
            if ($subNode instanceof PHPParser_NodeAbstract) {
                foreach ($visitors as $visitor) {
                    $visitor->enterNode($subNode);
                }
            }

            $this->_traverse($subNode, $visitors);

            if ($subNode instanceof PHPParser_NodeAbstract) {
                foreach ($visitors as $i => $visitor) {
                    $return = $visitor->leaveNode($subNode);

                    if (false === $return) {
                        $doNodes[] = array($subNodeKey, array());
                        break;
                    } elseif (is_array($return)) {
                        // traverse replacement nodes using all visitors apart from the one that
                        // did the change
                        $subNodeVisitors = $visitors;
                        unset($subNodeVisitors[$i]);
                        $this->_traverse($return, $subNodeVisitors);

                        $doNodes[] = array($subNodeKey, $return);
                        break;
                    }
                }
            }
        }

        if (!empty($doNodes)) {
            if (is_array($node)) {
                while (list($key, $replace) = array_pop($doNodes)) {
                    array_splice($node, $key, 1, $replace);
                }
            } else {
                while (list($key, $replace) = array_pop($doNodes)) {
                    if (!empty($replace)) {
                        throw new Exception('Nodes can only be merged if the parent is an array');
                    }

                    unset($node[$key]);
                }
            }
        }
    }
}