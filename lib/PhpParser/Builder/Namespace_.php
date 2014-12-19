<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Namespace_ extends PhpParser\BuilderAbstract
{
    private $fqn;

    public function __construct($fqn) {
        $this->fqn = $fqn;
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() {
        return new Stmt\Namespace_(
            new Node\Name($this->fqn),
            array(),
            array()
        );
    }

}