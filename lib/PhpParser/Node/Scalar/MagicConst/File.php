<?php

namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class File extends MagicConst
{
    public function getName() : string {
        return '__FILE__';
    }
}