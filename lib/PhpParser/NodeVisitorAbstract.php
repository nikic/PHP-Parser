<?php

namespace PhpParser;

/**
 * @codeCoverageIgnore
 */
class NodeVisitorAbstract implements NodeVisitor
{
    public function beforeTraverse(array $nodes)
    {
        return null;
    }

    public function enterNode(Node $node)
    {
        return null;
    }

    public function leaveNode(Node $node)
    {
        return null;
    }

    public function afterTraverse(array $nodes)
    {
        return null;
    }
}
