<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Name;

class NamespaceTest extends \PHPUnit_Framework_TestCase
{
    protected function createNamespaceBuilder($fqn) {
        return new Namespace_($fqn);
    }

    public function testNodeType() {
        $node = $this->createNamespaceBuilder('Some\Namespace')
            ->getNode()
        ;

        $this->assertInstanceOf('\PhpParser\Node\Stmt\Namespace_', $node);
    }
}