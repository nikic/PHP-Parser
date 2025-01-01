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
                $node->setAttribute('weak_parent', \WeakReference::create($parent));
            } else {
                $node->setAttribute('parent', $parent);
            }
        }

        if ($this->previous !== null) {
            if (
                $this->weakReferences
                && $this->previous->getAttribute('weak_parent') === $node->getAttribute('weak_parent')
            ) {
                $node->setAttribute('weak_previous', \WeakReference::create($this->previous));
                $this->previous->setAttribute('weak_next', \WeakReference::create($node));
            } elseif ($this->previous->getAttribute('parent') === $node->getAttribute('parent')) {
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
