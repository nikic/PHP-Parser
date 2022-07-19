<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;

class ParamTest extends \PHPUnit\Framework\TestCase
{
    public function testNoModifiers() {
        $node = new Param(new Variable('foo'));

        $this->assertFalse($node->isReadonly());
    }

    public function testReadonly() {
        $node = new Param(new Variable('foo'), null, null, false, false, [], Class_::MODIFIER_READONLY);

        $this->assertTrue($node->isReadonly());
    }
}
