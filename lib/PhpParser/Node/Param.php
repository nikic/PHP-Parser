<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Param extends NodeAbstract
{
    /** @var null|Identifier|Name|NullableType Typehint */
    public $type;
    /** @var bool Whether parameter is passed by reference */
    public $byRef;
    /** @var bool Whether this is a variadic argument */
    public $variadic;
    /** @var Expr\Variable Parameter variable */
    public $var;
    /** @var null|Expr Default value */
    public $default;

    /**
     * Constructs a parameter node.
     *
     * @param Expr\Variable                 $var        Parameter variable
     * @param null|Expr                     $default    Default value
     * @param null|string|Name|NullableType $type       Typehint
     * @param bool                          $byRef      Whether is passed by reference
     * @param bool                          $variadic   Whether this is a variadic argument
     * @param array                         $attributes Additional attributes
     */
    public function __construct(
        Expr\Variable $var, Expr $default = null, $type = null,
        bool $byRef = false, bool $variadic = false, array $attributes = []
    ) {
        parent::__construct($attributes);
        $this->type = \is_string($type) ? new Identifier($type) : $type;
        $this->byRef = $byRef;
        $this->variadic = $variadic;
        $this->var = $var;
        $this->default = $default;
    }

    public function getSubNodeNames() : array {
        return ['type', 'byRef', 'variadic', 'var', 'default'];
    }
    
    public function getType() : string {
        return 'Param';
    }
}
