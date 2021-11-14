<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\VariadicPlaceholder;

class CallableLikeTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideTestIsFirstClassCallable
     */
    public function testIsFirstClassCallable(CallLike $node, bool $isFirstClassCallable) {
        $this->assertSame($isFirstClassCallable, $node->isFirstClassCallable());
        if (!$isFirstClassCallable) {
            $this->assertSame($node->getRawArgs(), $node->getArgs());
        }
    }

    public function provideTestIsFirstClassCallable() {
        $normalArgs = [new Arg(new LNumber(1))];
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
}