<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class Escaped_ extends Scalar
{
    /** @var string String value */
    public $value;

    /**
     * Constructs a string scalar node.
     *
     * @param string $value      Value of the string
     * @param array  $attributes Additional attributes
     */
    public function __construct(string $value, array $attributes = []) {
        $this->attributes = $attributes;
        $this->value = $value;
    }

    public function getSubNodeNames() : array {
        return ['value'];
    }

    public function getType() : string {
        return 'Scalar_Escaped';
    }
}
