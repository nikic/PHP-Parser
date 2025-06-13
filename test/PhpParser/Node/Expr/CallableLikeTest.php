<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\VariadicPlaceholder;

class CallableLikeTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideTestIsFirstClassCallable
     */
    public function testIsFirstClassCallable(CallLike $node, bool $isFirstClassCallable): void {
        $this->assertSame($isFirstClassCallable, $node->isFirstClassCallable());
        if (!$isFirstClassCallable) {
            $this->assertSame($node->getRawArgs(), $node->getArgs());
        }
    }

    /**
     * @dataProvider provideTestGetArg
     */
    public function testGetArg(CallLike $node, ?Arg $expected): void {
        $this->assertSame($expected, $node->getArg('bar', 1));
    }

    public static function provideTestIsFirstClassCallable() {
        $normalArgs = [new Arg(new Int_(1))];
        $callableArgs = [new VariadicPlaceholder()];
        return [
            [new FuncCall(new Name('test'), $normalArgs), false],
            [new FuncCall(new Name('test'), $callableArgs), true],
            [new MethodCall(new Variable('this'), 'test', $normalArgs), false],
            [new MethodCall(new Variable('this'), 'test', $callableArgs), true],
            [new StaticCall(new Name('Test'), 'test', $normalArgs), false],
            [new StaticCall(new Name('Test'), 'test', $callableArgs), true],
            [new New_(new Name('Test'), $normalArgs), false],
            [new NullsafeMethodCall(new Variable('this'), 'test', $normalArgs), false],
            // This is not legal code, but accepted by the parser.
            [new New_(new Name('Test'), $callableArgs), true],
            [new NullsafeMethodCall(new Variable('this'), 'test', $callableArgs), true],
        ];
    }

    public static function provideTestGetArg() {
        $foo = new Arg(new Int_(1));
        $namedFoo = new Arg(new Int_(1), false, false, [], new Identifier('foo'));
        $bar = new Arg(new Int_(2));
        $namedBar = new Arg(new Int_(2), false, false, [], new Identifier('bar'));
        $unpack = new Arg(new Int_(3), false, true);
        $callableArgs = [new VariadicPlaceholder()];
        return [
            [new FuncCall(new Name('test'), [$foo]), null],
            [new FuncCall(new Name('test'), [$namedFoo]), null],
            [new FuncCall(new Name('test'), [$foo, $bar]), $bar],
            [new FuncCall(new Name('test'), [$namedBar]), $namedBar],
            [new FuncCall(new Name('test'), [$namedFoo, $namedBar]), $namedBar],
            [new FuncCall(new Name('test'), [$namedBar, $namedFoo]), $namedBar],
            [new FuncCall(new Name('test'), [$namedFoo, $unpack]), null],
            [new FuncCall(new Name('test'), $callableArgs), null],
            [new MethodCall(new Variable('this'), 'test', [$foo]), null],
            [new MethodCall(new Variable('this'), 'test', [$namedFoo]), null],
            [new MethodCall(new Variable('this'), 'test', [$foo, $bar]), $bar],
            [new MethodCall(new Variable('this'), 'test', [$namedBar]), $namedBar],
            [new MethodCall(new Variable('this'), 'test', [$namedFoo, $namedBar]), $namedBar],
            [new MethodCall(new Variable('this'), 'test', [$namedBar, $namedFoo]), $namedBar],
            [new MethodCall(new Variable('this'), 'test', [$namedFoo, $unpack]), null],
            [new MethodCall(new Variable('this'), 'test', $callableArgs), null],
            [new StaticCall(new Name('Test'), 'test', [$foo]), null],
            [new StaticCall(new Name('Test'), 'test', [$namedFoo]), null],
            [new StaticCall(new Name('Test'), 'test', [$foo, $bar]), $bar],
            [new StaticCall(new Name('Test'), 'test', [$namedBar]), $namedBar],
            [new StaticCall(new Name('Test'), 'test', [$namedFoo, $namedBar]), $namedBar],
            [new StaticCall(new Name('Test'), 'test', [$namedBar, $namedFoo]), $namedBar],
            [new StaticCall(new Name('Test'), 'test', [$namedFoo, $unpack]), null],
            [new StaticCall(new Name('Test'), 'test', $callableArgs), null],
            [new New_(new Name('test'), [$foo]), null],
            [new New_(new Name('test'), [$namedFoo]), null],
            [new New_(new Name('test'), [$foo, $bar]), $bar],
            [new New_(new Name('test'), [$namedBar]), $namedBar],
            [new New_(new Name('test'), [$namedFoo, $namedBar]), $namedBar],
            [new New_(new Name('test'), [$namedBar, $namedFoo]), $namedBar],
            [new New_(new Name('test'), [$namedFoo, $unpack]), null],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$foo]), null],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$namedFoo]), null],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$foo, $bar]), $bar],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$namedBar]), $namedBar],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$namedFoo, $namedBar]), $namedBar],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$namedBar, $namedFoo]), $namedBar],
            [new NullsafeMethodCall(new Variable('this'), 'test', [$namedFoo, $unpack]), null],
            // This is not legal code, but accepted by the parser.
            [new New_(new Name('Test'), $callableArgs), null],
            [new NullsafeMethodCall(new Variable('this'), 'test', $callableArgs), null],
        ];
    }
}
