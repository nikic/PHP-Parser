<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Visitor that connects a child node to its parent node
 * as well as its sibling nodes.
 *
 * On the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>, the previous
 * node can be accessed through <code>$node->getAttribute('previous')</code>,
 * and the next node can be accessed through <code>$node->getAttribute('next')</code>.
 */
final class NodeConnectingVisitor extends NodeVisitorAbstract {
    /**
     * @var Node[]
     */
    private array $stack = [];

    /**
     * @var ?Node
     */
    private $previous;

    private bool $weakReferences;

    public function __construct(bool $weakReferences = false) {
        $this->weakReferences = $weakReferences;
    }

    public function beforeTraverse(array $nodes) {
        $this->stack    = [];
        $this->previous = null;
    }

    public function enterNode(Node $node) {
        if (!empty($this->stack)) {
            $parent = $this->stack[count($this->stack) - 1];
            if ($this->weakReferences) {
                $parent = \WeakReference::create($parent);
            }
            $node->setAttribute('parent', $parent);
        }

        if ($this->previous !== null && $this->previous->getAttribute('parent') === $node->getAttribute('parent')) {
            if ($this->weakReferences) {
                $node->setAttribute('previous', \WeakReference::create($this->previous));
                $this->previous->setAttribute('next', \WeakReference::create($node));
            } else {
                $node->setAttribute('previous', $this->previous);
                $this->previous->setAttribute('next', $node);
            }
        }

        $this->stack[] = $node;
    }

    public function leaveNode(Node $node) {
        $this->previous = $node;

        array_pop($this->stack);
    }
}
