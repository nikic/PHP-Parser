<?php

/**
 * Interface for the ImmutableNodeTraverser.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface PHPParser_NodeTraversalCallback
{
    /**
     * Returns whether the given node's children should be traversed.
     *
     * Called in pre-order (before children)
     *
     * @param \PHPParser_Node $node
     *
     * @return Boolean
     */
    function shouldTraverse(PHPParser_Node $node);

    /**
     * Visits a given node.
     *
     * Called in post-order (children first)
     *
     * @param \PHPParser_Node $node
     */
    function visit(PHPParser_Node $node);
}