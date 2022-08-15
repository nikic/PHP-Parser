<?php declare(strict_types=1);

namespace PhpParser\Comment;

use PhpParser\Comment;
use PhpParser\Error;
use PhpParser\Node;

/**
 * The doc comment.
 */
class Doc extends Comment
{
    /** @var null|Node The comment node */
    protected $node;

    /**
     * @throws Error if the node not related to the comment
     */
    public function setNode(Node $node)
    {
        $comment = $node->getDocComment();
        if ($comment != $this) {
            throw new Error('The node not related to the comment.', $node->getAttributes());
        }
        $this->node = $node;
    }

    public function getNode() ?Node
    {
        return $this->node;
    }
}
