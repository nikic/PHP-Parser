<?php

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class LNumber extends Scalar
{
    /* For use in "kind" attribute */
    const KIND_BIN = 2;
    const KIND_OCT = 8;
    const KIND_DEC = 10;
    const KIND_HEX = 16;

    /** @var int Number value */
    public $value;

    /**
     * Constructs an integer number scalar node.
     *
     * @param int   $value      Value of the number
     * @param array $attributes Additional attributes
     */
    public function __construct($value, array $attributes = array()) {
        parent::__construct($attributes);
        $this->value = $value;
    }

    public function getSubNodeNames() {
        return array('value');
    }

    /**
     * Constructs an LNumber node from a string number literal.
     *
     * @param string $str        String number literal (decimal, octal, hex or binary)
     * @param array  $attributes Additional attributes
     *
     * @return LNumber The constructed LNumber, including kind attribute
     */
    public static function fromString($str, array $attributes = array()) {
        if ($str === '0' || $str[0] !== '0') {
            $attributes['kind'] = LNumber::KIND_DEC;
        } elseif ($str[1] === 'x' || $str[1] === 'X') {
            $attributes['kind'] = LNumber::KIND_HEX;
        } elseif ($str[1] === 'b' || $str[1] === 'B') {
            $attributes['kind'] = LNumber::KIND_BIN;
        } else {
            $attributes['kind'] = LNumber::KIND_OCT;
        }
        return new self(self::parse($str), $attributes);
    }

    /**
     * @internal
     *
     * Parses an LNUMBER token (dec, hex, oct and bin notations) like PHP would.
     *
     * @param string $str A string number
     *
     * @return int The parsed number
     */
    public static function parse($str) {
        // handle plain 0 specially
        if ('0' === $str) {
            return 0;
        }

        // if first char is 0 (and number isn't 0) it's a special syntax
        if ('0' === $str[0]) {
            // hex
            if ('x' === $str[1] || 'X' === $str[1]) {
                return hexdec($str);
            }

            // bin
            if ('b' === $str[1] || 'B' === $str[1]) {
                return bindec($str);
            }

            // oct (intval instead of octdec to get proper cutting behavior with malformed numbers)
            return intval($str, 8);
        }

        // dec
        return (int) $str;
    }
}
