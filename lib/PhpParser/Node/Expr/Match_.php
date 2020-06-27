<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Stmt\MatchArm;

class Match_ extends Node\Expr
{
    /** @var Node\Expr */
    public $cond;
    /** @var MatchArm[] */
    public $matchArms;

    /**
     * @param MatchArm[] $matchArms
     */
    public function __construct(Node\Expr $cond, array $matchArms = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->matchArms = $matchArms;
    }

    public function getSubNodeNames() : array {
        return ['cond', 'matchArms'];
    }

    public function getType() : string {
        return 'Expr_Match';
    }
}
