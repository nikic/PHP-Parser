<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var string Method name */
    public $method;
}
