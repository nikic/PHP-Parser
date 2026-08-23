<?php declare(strict_types=1);

namespace PhpParser;

/**
 * @codeCoverageIgnore
 */
abstract class NodeVisitorAbstract implements NodeVisitor {
    /**
     * @inheritDoc
     */
    public function beforeTraverse(array $nodes) {
        return null;
    }
    /**
     * @inheritDoc
     */
    public function enterNode(Node $node) {
        return null;
    }
    /**
     * @inheritDoc
     */
    public function leaveNode(Node $node) {
        return null;
    }
    /**
     * @inheritDoc
     */
    public function afterTraverse(array $nodes) {
        return null;
    }
}
