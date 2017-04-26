<?php

namespace PhpParser\Node\Stmt;

use PHPUnit\Framework\TestCase;

class ClassConstTest extends TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier) {
        $node = new ClassConst(
            array(), // invalid
            constant('PhpParser\Node\Stmt\Class_::MODIFIER_' . strtoupper($modifier))
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers() {
        $node = new ClassConst(array(), 0);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
    }

    public function provideModifiers() {
        return array(
            array('public'),
            array('protected'),
            array('private'),
        );
    }
}
