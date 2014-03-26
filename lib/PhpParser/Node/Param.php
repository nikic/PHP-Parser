<?php

namespace PhpParser\Node;

use PhpParser\Error;
use PhpParser\NodeAbstract;

/**
 * @property null|string|Name $type     Typehint
 * @property bool             $byRef    Whether is passed by reference
 * @property bool             $variadic Whether this is a variadic argument
 * @property string           $name     Name
 * @property null|Expr        $default  Default value
 */
class Param extends NodeAbstract
{
    /**
     * Constructs a parameter node.
     *
     * @param string           $name       Name
     * @param null|Expr        $default    Default value
     * @param null|string|Name $type       Typehint
     * @param bool             $byRef      Whether is passed by reference
     * @param bool             $variadic   Whether this is a variadic argument
     * @param array            $attributes Additional attributes
     */
    public function __construct($name, $default = null, $type = null, $byRef = false, $variadic = false, array $attributes = array()) {
        parent::__construct(
            array(
                'type'     => $type,
                'byRef'    => $byRef,
                'variadic' => $variadic,
                'name'     => $name,
                'default'  => $default,
            ),
            $attributes
        );

        if ($variadic && null !== $default) {
            throw new Error('Variadic parameter cannot have a default value');
        }
    }
}