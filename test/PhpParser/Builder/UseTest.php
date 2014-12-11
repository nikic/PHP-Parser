<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Name;

class UseTest extends \PHPUnit_Framework_TestCase
{
    protected function createUseBuilder($fqn) {
        return new Use_($fqn);
    }

    public function testNodeType() {
        $node = $this->createUseBuilder('Some\Namespaced\Class')
            ->getNode()
        ;

        $this->assertInstanceOf('\PhpParser\Node\Stmt\Use_', $node);
        $this->assertInstanceOf('\PhpParser\Node\Stmt\UseUse', $node->uses[0]);
        $this->assertEquals($node->uses[0]->name, 'Some\Namespaced\Class');
    }
}