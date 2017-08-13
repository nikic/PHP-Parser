<?php

declare(strict_types=1);

namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Line extends MagicConst
{
    public function getName() : string {
        return '__LINE__';
    }
}