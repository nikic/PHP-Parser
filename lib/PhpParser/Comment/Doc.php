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
    /** @var null|Node The related node */
    protected $relatedNode;

    /**
     * @throws Error if the node not related to the doc comment
     */
    public function setRelatedNode(Node $node) {
        $comment = $node->getDocComment();
        if ($comment != $this) {
            throw new Error('The node not related to the doc comment.', $node->getAttributes());
        }
        $this->relatedNode = $node;
    }

    public function getRelatedNode() : ?Node {
        return $this->relatedNode;
    }
}
