<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Stmt;

class Use_ extends \PhpParser\BuilderAbstract
{

    private $fqcn;

    public function __construct($fqcn) {
        $this->fqcn = $fqcn;
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() {
        return new Stmt\Use_(array(
            new Stmt\UseUse(
                new Node\Name($this->fqcn)
            ),
        ));
    }

}