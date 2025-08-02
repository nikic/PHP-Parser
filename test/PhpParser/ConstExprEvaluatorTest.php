<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ConstExprEvaluatorTest extends \PHPUnit\Framework\TestCase {
    /** @dataProvider provideTestEvaluate */
    public function testEvaluate($exprString, $expected): void {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $expr = $parser->parse('<?php ' . $exprString . ';')[0]->expr;
        $evaluator = new ConstExprEvaluator();
        $this->assertSame($expected, $evaluator->evaluateDirectly($expr));
    }

    public static function provideTestEvaluate() {
        return [
            ['1', 1],
            ['1.0', 1.0],
            ['"foo"', "foo"],
            ['[0, 1]', [0, 1]],
            ['["foo" => "bar"]', ["foo" => "bar"]],
            ['[...["bar"]]', ["bar"]],
            ['[...["foo" => "bar"]]', ["foo" => "bar"]],
            ['["a", "b" => "b", ...["b" => "bb", "c"]]', ["a", "b" => "bb", "c"]],
            ['NULL', null],
            ['False', false],
            ['true', true],
            ['+1', 1],
            ['-1', -1],
            ['~0', -1],
            ['!true', false],
            ['[0][0]', 0],
            ['"a"[0]', "a"],
            ['true ? 1 : (1/0)', 1],
            ['false ? (1/0) : 1', 1],
            ['42 ?: (1/0)', 42],
            ['false ?: 42', 42],
            ['false ?? 42', false],
            ['null ?? 42', 42],
            ['[0][0] ?? 42', 0],
            ['[][0] ?? 42', 42],
            ['0b11 & 0b10', 0b10],
            ['0b11 | 0b10', 0b11],
            ['0b11 ^ 0b10', 0b01],
            ['1 << 2', 4],
            ['4 >> 2', 1],
            ['"a" . "b"', "ab"],
            ['4 + 2', 6],
            ['4 - 2', 2],
            ['4 * 2', 8],
            ['4 / 2', 2],
            ['4 % 2', 0],
            ['4 ** 2', 16],
            ['1 == 1.0', true],
            ['1 != 1.0', false],
            ['1 < 2.0', true],
            ['1 <= 2.0', true],
            ['1 > 2.0', false],
            ['1 >= 2.0', false],
            ['1 <=> 2.0', -1],
            ['1 === 1.0', false],
            ['1 !== 1.0', true],
            ['true && true', true],
            ['true and true', true],
            ['false && (1/0)', false],
            ['false and (1/0)', false],
            ['false || false', false],
            ['false or false', false],
            ['true || (1/0)', true],
            ['true or (1/0)', true],
            ['true xor false', true],
            ['"foo" |> "strlen"', 3],
        ];
    }

    public function testEvaluateFails(): void {
        $this->expectException(ConstExprEvaluationException::class);
        $this->expectExceptionMessage('Expression of type Expr_Variable cannot be evaluated');
        $evaluator = new ConstExprEvaluator();
        $evaluator->evaluateDirectly(new Expr\Variable('a'));
    }

    public function testEvaluateFallback(): void {
        $evaluator = new ConstExprEvaluator(function (Expr $expr) {
            if ($expr instanceof Scalar\MagicConst\Line) {
                return 42;
            }
            throw new ConstExprEvaluationException();
        });
        $expr = new Expr\BinaryOp\Plus(
            new Scalar\Int_(8),
            new Scalar\MagicConst\Line()
        );
        $this->assertSame(50, $evaluator->evaluateDirectly($expr));
    }

    /**
     * @dataProvider provideTestEvaluateSilently
     */
    public function testEvaluateSilently($expr, $exception, $msg): void {
        $evaluator = new ConstExprEvaluator();

        try {
            $evaluator->evaluateSilently($expr);
        } catch (ConstExprEvaluationException $e) {
            $this->assertSame(
                'An error occurred during constant expression evaluation',
                $e->getMessage()
            );

            $prev = $e->getPrevious();
            $this->assertInstanceOf($exception, $prev);
            $this->assertSame($msg, $prev->getMessage());
        }
    }

    public static function provideTestEvaluateSilently() {
        return [
            [
                new Expr\BinaryOp\Mod(new Scalar\Int_(42), new Scalar\Int_(0)),
                \Error::class,
                'Modulo by zero'
            ],
            [
                new Expr\BinaryOp\Plus(new Scalar\Int_(42), new Scalar\String_("1foo")),
                \ErrorException::class,
                \PHP_VERSION_ID >= 80000
                    ? 'A non-numeric value encountered'
                    : 'A non well formed numeric value encountered'
            ],
        ];
    }
}
