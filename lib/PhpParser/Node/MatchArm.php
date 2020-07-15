<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class MatchArm extends NodeAbstract
{
    /** @var null|Node\Expr[] */
    public $condList;
    /** @var Node\Expr */
    public $body;

    /**
     * @param null|Node\Expr[] $condList
     */
    public function __construct($condList, Node\Expr $body, array $attributes = []) {
        $this->condList = $condList;
        $this->body = $body;
        $this->attributes = $attributes;
    }

    public function getSubNodeNames() : array {
        return ['condList', 'body'];
    }

    public function getType() : string {
        return 'MatchArm';
    }
}
