<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * @property null|Node\Expr $num Number of loops to continue
 */
class Continue_ extends Node\Stmt
{
    /**
     * Constructs a continue node.
     *
     * @param null|Node\Expr $num        Number of loops to continue
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