<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class EncapsedStringPart extends Scalar
{
    /** @var string String value */
    public $value;

    /**
     * Constructs a node representing a string part of an encapsed string.
     *
     * @param string $value      String value
     * @param array  $attributes Additional attributes
     */
    public function __construct(string $value, array $attributes = []) {
        $this->attributes = $attributes;
        $this->value = $value;
    }

    public static function fromString(string $value, array $attributes): self
    {
        $attributes['rawValue'] = $value;

        return new self($value, $attributes);
    }

    /**
     * @param null|string $quote Quote type
     * @param bool $parseUnicodeEscape Whether to parse PHP 7 \u escapes
     */
    public static function fromStringParsed(string $value, array $attributes, $quote, bool $parseUnicodeEscape = true): self
    {
        $attributes['rawValue'] = $value;
        $parsedValue = String_::parseEscapeSequences($value, $quote, $parseUnicodeEscape);

        return new self($parsedValue, $attributes);
    }

    public function getSubNodeNames() : array {
        return ['value'];
    }

    public function getType() : string {
        return 'Scalar_EncapsedStringPart';
    }
}
