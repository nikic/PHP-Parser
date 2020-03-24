<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class DNumber extends Scalar
{
    /** @var float Number value */
    public $value;

    /**
     * Constructs a float number scalar node.
     *
     * @param float $value      Value of the number
     * @param array $attributes Additional attributes
     */
    public function __construct(float $value, array $attributes = []) {
        $this->attributes = $attributes;
        $this->value = $value;
    }

    public function getSubNodeNames() : array {
        return ['value'];
    }

    /**
     * @internal
     *
     * Parses a DNUMBER token like PHP would.
     *
     * @param string $str           A string number
     * @param array  $attributes    Additional attributes
     */
    public static function fromString(string $str, array $attributes = []) : DNumber {
        $attributes['original_value'] = $str;

        $str = str_replace('_', '', $str);

        // if string contains any of .eE just cast it to float
        if (false !== strpbrk($str, '.eE')) {

            return new DNumber((float) $str, $attributes);
        }

        // otherwise it's an integer notation that overflowed into a float
        // if it starts with 0 it's one of the special integer notations
        if ('0' === $str[0]) {
            // hex
            if ('x' === $str[1] || 'X' === $str[1]) {
                return new DNumber(hexdec($str), $attributes);
            }

            // bin
            if ('b' === $str[1] || 'B' === $str[1]) {
                return new DNumber(bindec($str), $attributes);
            }

            // oct
            // substr($str, 0, strcspn($str, '89')) cuts the string at the first invalid digit (8 or 9)
            // so that only the digits before that are used
            $octdecValue = octdec(substr($str, 0, strcspn($str, '89')));

            return new DNumber($octdecValue, $attributes);
        }

        // dec
        return new DNumber((float) $str, $attributes);
    }

    public function getType() : string {
        return 'Scalar_DNumber';
    }
}
