<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * @property string           $name    Name
 * @property null|Expr        $default Default value
 * @property null|string|Name $type    Typehint
 * @property bool             $byRef   Whether is passed by reference
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
     * @param array            $attributes Additional attributes
     */
    public function __construct($name, $default = null, $type = null, $byRef = false, array $attributes = array()) {
        parent::__construct(
            array(
                'name'    => $name,
                'default' => $default,
                'type'    => $type,
                'byRef'   => $byRef
            ),
            $attributes
        );
    }
}