<?php

interface PHPParser_NodeVisitorInterface
{
    /**
     * Called once before traversal.
     *
     * @param $node
     */
    public function beforeTraverse(&$node);

    /**
     * Called when entering a node.
     *
     * @param PHPParser_Node $node
     */
    public function enterNode(PHPParser_Node &$node);

    /**
     * Called when leaving a node.
     *
     * @param PHPParser_Node $node
     */
    public function leaveNode(PHPParser_Node &$node);

    /**
     * Called once after traversal.
     *
     * @param $node
     */
    public function afterTraverse(&$node);
}