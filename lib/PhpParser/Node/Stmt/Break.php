<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property null|Node\Expr $num Number of loops to break
 */
class Break_ extends Node\Stmt
{
    /**
     * Constructs a break node.
     *
     * @param null|Node\Expr $num        Number of loops to break
     * @param array          $attributes Additional attributes
     */
    public function __construct(Node\Expr $num = null, array $attributes = array()) {
        parent::__construct(
            array(
                'num' => $num,
            ),
            $attributes
        );
    }
}