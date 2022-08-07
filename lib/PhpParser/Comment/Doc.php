<?php declare(strict_types=1);

namespace PhpParser\Comment;

class Doc extends \PhpParser\Comment
{
    /**
     * @inheritdoc
     */
    public function jsonSerialize() : array {
        return ['nodeType' => 'Comment_Doc'] + parent::jsonSerialize();
    }
}
