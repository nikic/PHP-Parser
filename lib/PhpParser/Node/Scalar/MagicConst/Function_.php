<?php

declare(strict_types=1);

namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Function_ extends MagicConst
{
    public function getName() : string {
        return '__FUNCTION__';
    }
}