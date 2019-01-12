<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ConstExprEvaluatorTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider provideTestEvaluate */
    public function testEvaluate($exprString, $expected) {
        $parser = new Parser\Php7(new Lexer());
        $expr = $parser->parse('<?php ' . $exprString . ';')[0]->expr;
        $evaluator = new ConstExprEvaluator();
        $this->assertSame($expected, $evaluator->evaluateDirectly($expr));
    }

    public function provideTestEvaluate(): \Iterator
    {
        yield ['1', 1];
        yield ['1.0', 1.0];
        yield ['"foo"', "foo"];
        yield ['[0, 1]', [0, 1]];
        yield ['["foo" => "bar"]', ["foo" => "bar"]];
        yield ['NULL', null];
        yield ['False', false];
        yield ['true', true];
        yield ['+1', 1];
        yield ['-1', -1];
        yield ['~0', -1];
        yield ['!true', false];
        yield ['[0][0]', 0];
        yield ['"a"[0]', "a"];
        yield ['true ? 1 : (1/0)', 1];
        yield ['false ? (1/0) : 1', 1];
        yield ['42 ?: (1/0)', 42];
        yield ['false ?: 42', 42];
        yield ['false ?? 42', false];
        yield ['null ?? 42', 42];
        yield ['[0][0] ?? 42', 0];
        yield ['[][0] ?? 42', 42];
        yield ['0b11 & 0b10', 0b10];
        yield ['0b11 | 0b10', 0b11];
        yield ['0b11 ^ 0b10', 0b01];
        yield ['1 << 2', 4];
        yield ['4 >> 2', 1];
        yield ['"a" . "b"', "ab"];
        yield ['4 + 2', 6];
        yield ['4 - 2', 2];
        yield ['4 * 2', 8];
        yield ['4 / 2', 2];
        yield ['4 % 2', 0];
        yield ['4 ** 2', 16];
        yield ['1 == 1.0', true];
        yield ['1 != 1.0', false];
        yield ['1 < 2.0', true];
        yield ['1 <= 2.0', true];
        yield ['1 > 2.0', false];
        yield ['1 >= 2.0', false];
        yield ['1 <=> 2.0', -1];
        yield ['1 === 1.0', false];
        yield ['1 !== 1.0', true];
        yield ['true && true', true];
        yield ['true and true', true];
        yield ['false && (1/0)', false];
        yield ['false and (1/0)', false];
        yield ['false || false', false];
        yield ['false or false', false];
        yield ['true || (1/0)', true];
        yield ['true or (1/0)', true];
        yield ['true xor false', true];
    }

    public function testEvaluateFails() {
        $this->expectException(ConstExprEvaluationException::class);
        $this->expectExceptionMessage('Expression of type Expr_Variable cannot be evaluated');
        $evaluator = new ConstExprEvaluator();
        $evaluator->evaluateDirectly(new Expr\Variable('a'));
    }

    public function testEvaluateFallback() {
        $evaluator = new ConstExprEvaluator(function(Expr $expr) {
            if ($expr instanceof Scalar\MagicConst\Line) {
                return 42;
            }
            throw new ConstExprEvaluationException();
        });
        $expr = new Expr\BinaryOp\Plus(
            new Scalar\LNumber(8),
            new Scalar\MagicConst\Line()
        );
        $this->assertSame(50, $evaluator->evaluateDirectly($expr));
    }

    /**
     * @dataProvider provideTestEvaluateSilently
     */
    public function testEvaluateSilently($expr, $exception, $msg) {
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

    public function provideTestEvaluateSilently(): \Iterator
    {
        yield [
            new Expr\BinaryOp\Mod(new Scalar\LNumber(42), new Scalar\LNumber(0)),
            \Error::class,
            'Modulo by zero'
        ];
        yield [
            new Expr\BinaryOp\Div(new Scalar\LNumber(42), new Scalar\LNumber(0)),
            \ErrorException::class,
            'Division by zero'
        ];
    }
}
