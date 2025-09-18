<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ExprEvaluatorTest extends \PHPUnit\Framework\TestCase {
    /** @dataProvider provideTestEvaluate */
    public function testEvaluate($exprString, $expected): void {
	    global $globalNotDeclaredVar;

	    global $globalNonNullVar;
	    $globalNonNullVar="a";

	    global $globalArray;
	    $globalArray=["gabu" => "zomeu"];

	    global $globalNullVar;
	    $globalNullVar=null;

	    global $globalEvaluationFakeClass;
	    $globalEvaluationFakeClass = new EvaluationFakeClass();

	    $oNonNullVar2="a";
	    $oNullVar2=null;

	    $parser = (new ParserFactory())->createForNewestSupportedVersion();
	    $expr = $parser->parse('<?php ' . $exprString . ';')[0]->expr;
	    $evaluator = new ExprEvaluator();
	    $evaluator->setStaticCallsWhitelist(["PhpParser\EvaluationFakeClass::GetStaticValue"]);
	    $evaluator->setFunctionsWhitelist(["class_exists"]);
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
            ['True', true],
            ['true', true],
            ['PHP_VERSION_ID', PHP_VERSION_ID],
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

			//Variable
	        ['$globalNonNullVar', "a"],
	        ['$globalNotDeclaredVar', null],
	        ['$globalArray', ["gabu" => "zomeu"]],
	        ['$globalNullVar', null],
	        ['$globalNonNullVar', "a"],
	        //Isset
	        ['isset($globalNotDeclaredVar)', false],
	        ['isset($globalNonNullVar)', true],
	        ['isset($globalArray)', true],
	        ['isset($globalNullVar)', false],
	        ['isset($globalNonNullVar)', true],
	        ['isset($oNonNullVar)', false],
	        ['isset($oNullVar)', false],
	        ['isset($eee)', false],
	        //Cast
	        ['(int)true', 1],
	        ['(string)1', "1"],
	        ['(bool)1', true],
	        ['(double)1', 1.0],
	        ['(float)1', 1.0],
	        ['(string) $globalEvaluationFakeClass', "toString"],
	        ['PhpParser\EvaluationFakeClass::CONST_4TEST', 456],
	        ['UnexistingClass::class', "UnexistingClass"],
	        ['PhpParser\EvaluationFakeClass::$STATICPROPERTY_4TEST', 123],
	        ['PhpParser\EvaluationFakeClass::GetStaticValue()', "shadok"],
	        ['class_exists("PhpParser\EvaluationFakeClass")', true],
	        ['$globalEvaluationFakeClass->iIsOk', 'IsOkValue'],
	        ['$globalNullVar?->iIsOk', null],
	        ['$globalEvaluationFakeClass->GetName()', 'gabuzomeu'],
	        ['$globalNullVar?->GetName()', null],
	        ['$globalEvaluationFakeClass->GetLongName("aa")', 'gabuzomeu_aa'],
	        ['$globalNullVar??1', 1],
	        ['$globalNotDeclaredVar??1', 1],
	        ['$globalNotDeclaredVar["a"]??1', 1],
	        ['$globalArray["gabu"]??1', "zomeu"],
	        ['$globalNonNullVar??1', "a"],
        ];
    }

    public function testEvaluateFails(): void {
        $this->expectException(ExprEvaluationException::class);
        $this->expectExceptionMessage('Expression of type Expr_Variable cannot be evaluated');
        $evaluator = new ExprEvaluator();
		$evaluator->evaluateDirectly(new Expr\Variable('a'));
    }

	public function testEvaluateStaticCallOutsideWhitelistFails(): void {
		$this->expectException(ExprEvaluationException::class);
		$this->expectExceptionMessage('Expression of type Expr_StaticCall cannot be evaluated');

		$parser = (new ParserFactory())->createForNewestSupportedVersion();
		$exprString = "PhpParser\EvaluationFakeClass::GetStaticValue()";
		$expr = $parser->parse('<?php ' . $exprString . ';')[0]->expr;
		$evaluator = new ExprEvaluator();
		$evaluator->evaluateDirectly($expr);
	}

	public function testEvaluateFuncCallOutsideWhitelistFails(): void {
		$this->expectException(ExprEvaluationException::class);
		$this->expectExceptionMessage('Expression of type Expr_FuncCall cannot be evaluated');

		$parser = (new ParserFactory())->createForNewestSupportedVersion();
		$exprString = 'class_exists("PhpParser\EvaluationFakeClass")';
		$expr = $parser->parse('<?php ' . $exprString . ';')[0]->expr;
		$evaluator = new ExprEvaluator();
		$evaluator->evaluateDirectly($expr);
	}

    public function testEvaluateFallback(): void {
        $evaluator = new ExprEvaluator(function (Expr $expr) {
            if ($expr instanceof Scalar\MagicConst\Line) {
                return 42;
            }
            throw new ExprEvaluationException();
        });
        $expr = new Expr\BinaryOp\Plus(
            new Scalar\Int_(8),
            new Scalar\MagicConst\Line()
        );
        $this->assertSame(50, $evaluator->evaluateDirectly($expr));
    }
}

class EvaluationFakeClass {
	public static $STATICPROPERTY_4TEST = 123;
	const CONST_4TEST = 456;

	public string $iIsOk = "IsOkValue";

	public static function GetStaticValue(){
		return "shadok";
	}

	public function GetName() {
		return "gabuzomeu";
	}

	public function GetLongName($suffix) {
		return "gabuzomeu_".$suffix;
	}

	public function __toString(): string {
		return "toString";
	}
}
