<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Trait_ extends MagicConst
{
    public function getName() : string {
        return '__TRAIT__';
    }
    
    function getType() : string {
        return 'Scalar_MagicConst_Trait';
    }
}