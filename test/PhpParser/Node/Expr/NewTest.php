<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

class NewTest extends TestCase
{
    public function testNewFuncCall()
    {
        $node = new New_(new FuncCall(new Name('get_class'), [
            new Arg(new Variable('object')),
        ]), [
            new Arg(new Variable('a')),
            new Arg(new Variable('b')),
        ]);
        $pp = new Standard;
        $code = $pp->prettyPrint([$node]);
        $this->assertSame('new (get_class($object))($a, $b)', $code);
    }

    public function testNewMethodCall()
    {
        $node = new New_(new MethodCall(new Variable('this'), 'className', [
            new Arg(new Variable('a')),
        ]), [
            new Arg(new Variable('b')),
            new Arg(new Variable('c')),
        ]);
        $pp = new Standard;
        $code = $pp->prettyPrint([$node]);
        $this->assertSame('new ($this->className($a))($b, $c)', $code);
    }
}
