<?php

abstract class PHPParser_BuilderAbstract implements PHPParser_Builder {
    /**
     * Normalizes a node: Converts builder objects to nodes.
     *
     * @param PHPParser_Node|PHPParser_Builder $node The node to normalize
     *
     * @return PHPParser_Node The normalized node
     */
    protected function normalizeNode($node) {
        if ($node instanceof PHPParser_Builder) {
            return $node->getNode();
        } elseif ($node instanceof PHPParser_Node) {
            return $node;
        }

        throw new LogicException('Expected node or builder object');
    }

    /**
     * Sets a modifier in the $this->type property.
     *
     * @param int $modifier Modifier to set
     */
    protected function setModifier($modifier) {
        PHPParser_Node_Stmt_Class::verifyModifier($this->type, $modifier);
        $this->type |= $modifier;
    }
}