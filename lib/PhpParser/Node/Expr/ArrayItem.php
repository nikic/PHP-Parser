<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ArrayItem extends Expr
{
    /** @var null|Expr Key */
    public $key;
    /** @var Expr Value */
    public $value;
    /** @var bool Whether to assign by reference */
    public $byRef;

    /**
     * Constructs an array item node.
     *
     * @param Expr      $value      Value
     * @param null|Expr $key        Key
     * @param bool      $byRef      Whether to assign by reference
     * @param array     $attributes Additional attributes
     */
    public function __construct(Expr $value, Expr $key = null, bool $byRef = false, array $attributes = []) {
        parent::__construct($attributes);
        $this->key = $key;
        $this->value = $value;
        $this->byRef = $byRef;
    }

    public function getSubNodeNames() : array {
        return ['key', 'value', 'byRef'];
    }
    
    public function getType() : string {
        return 'Expr_ArrayItem';
    }
}
