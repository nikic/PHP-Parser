<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Stmt;

class NamespaceTest extends \PHPUnit_Framework_TestCase
{
    protected function createNamespaceBuilder($fqn) {
        return new Namespace_($fqn);
    }

    public function testCreation() {
        $stmt1 = new Stmt\Class_('SomeClass');
        $stmt2 = new Stmt\Interface_('SomeInterface');
        $stmt3 = new Stmt\Function_('someFunction');
        $expected = new Stmt\Namespace_(
            new Node\Name('Name\Space'),
            array($stmt1, $stmt2, $stmt3)
        );

        $node = $this->createNamespaceBuilder('Name\Space')
            ->addStatement($stmt1)
            ->addStatements(array($stmt2, $stmt3))
            ->getNode()
        ;
        $this->assertEquals($expected, $node);

        $node = $this->createNamespaceBuilder(new Node\Name(array('Name', 'Space')))
            ->addStatements(array($stmt1, $stmt2))
            ->addStatement($stmt3)
            ->getNode()
        ;
        $this->assertEquals($expected, $node);

        $node = $this->createNamespaceBuilder(null)->getNode();
        $this->assertNull($node->name);
        $this->assertEmpty($node->stmts);
    }
}
