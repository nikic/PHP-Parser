<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use Exception;

use function array_merge;

/**
 * Evaluates constant expressions.
 *
 * This evaluator is able to evaluate all constant expressions (as defined by PHP), which can be
 * evaluated without further context. If a subexpression is not of this type, a user-provided
 * fallback evaluator is invoked. To support all constant expressions that are also supported by
 * PHP (and not already handled by this class), the fallback evaluator must be able to handle the
 * following node types:
 *
 *  * All Scalar\MagicConst\* nodes.
 *  * Expr\ConstFetch nodes. Only null/false/true are already handled by this class.
 *  * Expr\ClassConstFetch nodes.
 *
 * The fallback evaluator should throw ConstExprEvaluationException for nodes it cannot evaluate.
 *
 * The evaluation is dependent on runtime configuration in two respects: Firstly, floating
 * point to string conversions are affected by the precision ini setting. Secondly, they are also
 * affected by the LC_NUMERIC locale.
 */
class ConstExprEvaluator {
    /** @var callable|null */
    private $fallbackEvaluator;

	/** @var array $functionsWhiteList */
    private $functionsWhiteList;

	/** @var array $staticCallsWhitelist */
	private $staticCallsWhitelist;

    /**
     * Create a constant expression evaluator.
     *
     * The provided fallback evaluator is invoked whenever a subexpression cannot be evaluated. See
     * class doc comment for more information.
     *
     * @param callable|null $fallbackEvaluator To call if subexpression cannot be evaluated
     */
    public function __construct(?callable $fallbackEvaluator = null) {
        $this->fallbackEvaluator = $fallbackEvaluator ?? function (Expr $expr) {
            throw new ConstExprEvaluationException(
                "Expression of type {$expr->getType()} cannot be evaluated"
            );
        };

		$this->functionsWhiteList = [];
		$this->staticCallsWhitelist = [];
    }

	public function setFunctionsWhitelist(array $functionsWhiteList): void
	{
		$this->functionsWhiteList = $functionsWhiteList;
	}

	public function setStaticCallsWhitelist(array $staticCallsWhitelist): void
	{
		$this->staticCallsWhitelist = $staticCallsWhitelist;
    }

    /**
     * Silently evaluates a constant expression into a PHP value.
     *
     * Thrown Errors, warnings or notices will be converted into a ConstExprEvaluationException.
     * The original source of the exception is available through getPrevious().
     *
     * If some part of the expression cannot be evaluated, the fallback evaluator passed to the
     * constructor will be invoked. By default, if no fallback is provided, an exception of type
     * ConstExprEvaluationException is thrown.
     *
     * See class doc comment for caveats and limitations.
     *
     * @param Expr $expr Constant expression to evaluate
     * @return mixed Result of evaluation
     *
     * @throws ConstExprEvaluationException if the expression cannot be evaluated or an error occurred
     */
    public function evaluateSilently(Expr $expr) {
        set_error_handler(function ($num, $str, $file, $line) {
            throw new \ErrorException($str, 0, $num, $file, $line);
        });

        try {
            return $this->evaluate($expr);
        } catch (\Throwable $e) {
            if (!$e instanceof ConstExprEvaluationException) {
                $e = new ConstExprEvaluationException(
                    "An error occurred during constant expression evaluation", 0, $e);
            }
            throw $e;
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Directly evaluates a constant expression into a PHP value.
     *
     * May generate Error exceptions, warnings or notices. Use evaluateSilently() to convert these
     * into a ConstExprEvaluationException.
     *
     * If some part of the expression cannot be evaluated, the fallback evaluator passed to the
     * constructor will be invoked. By default, if no fallback is provided, an exception of type
     * ConstExprEvaluationException is thrown.
     *
     * See class doc comment for caveats and limitations.
     *
     * @param Expr $expr Constant expression to evaluate
     * @return mixed Result of evaluation
     *
     * @throws ConstExprEvaluationException if the expression cannot be evaluated
     */
    public function evaluateDirectly(Expr $expr) {
        return $this->evaluate($expr);
    }

    /** @return mixed */
    private function evaluate(Expr $expr) {
        if ($expr instanceof Scalar\Int_
            || $expr instanceof Scalar\Float_
            || $expr instanceof Scalar\String_
        ) {
            return $expr->value;
        }

        if ($expr instanceof Expr\Array_) {
            return $this->evaluateArray($expr);
        }

	    if ($expr instanceof Expr\Variable) {
		    return $this->evaluateVariable($expr);
	    }

        // Unary operators
        if ($expr instanceof Expr\UnaryPlus) {
            return +$this->evaluate($expr->expr);
        }
        if ($expr instanceof Expr\UnaryMinus) {
            return -$this->evaluate($expr->expr);
        }
        if ($expr instanceof Expr\BooleanNot) {
            return !$this->evaluate($expr->expr);
        }
        if ($expr instanceof Expr\BitwiseNot) {
            return ~$this->evaluate($expr->expr);
        }

        if ($expr instanceof Expr\BinaryOp) {
            return $this->evaluateBinaryOp($expr);
        }

        if ($expr instanceof Expr\Ternary) {
            return $this->evaluateTernary($expr);
        }

        if ($expr instanceof Expr\ArrayDimFetch && null !== $expr->dim) {
            return $this->evaluate($expr->var)[$this->evaluate($expr->dim)];
        }

        if ($expr instanceof Expr\ConstFetch) {
            return $this->evaluateConstFetch($expr);
        }

	    if ($expr instanceof Expr\Isset_) {
		    return $this->evaluateIsset($expr);
	    }

	    if ($expr instanceof Expr\ClassConstFetch) {
		    return $this->evaluateClassConstFetch($expr);
	    }

	    if ($expr instanceof Expr\Cast) {
		    return $this->evaluateCast($expr);
	    }

	    if ($expr instanceof Expr\StaticPropertyFetch) {
		    return $this->evaluateStaticPropertyFetch($expr);
	    }

	    if ($expr instanceof Expr\FuncCall) {
		    return $this->evaluateFuncCall($expr);
	    }

	    if ($expr instanceof Expr\StaticCall) {
		    return $this->evaluateStaticCall($expr);
	    }

	    if ($expr instanceof Expr\NullsafePropertyFetch||$expr instanceof Expr\PropertyFetch) {
		    return $this->evaluatePropertyFetch($expr);
	    }

	    if ($expr instanceof Expr\NullsafeMethodCall||$expr instanceof Expr\MethodCall) {
		    return $this->evaluateMethodCall($expr);
	    }
        return ($this->fallbackEvaluator)($expr);
    }

    private function evaluateArray(Expr\Array_ $expr): array {
        $array = [];
        foreach ($expr->items as $item) {
            if (null !== $item->key) {
                $array[$this->evaluate($item->key)] = $this->evaluate($item->value);
            } elseif ($item->unpack) {
                $array = array_merge($array, $this->evaluate($item->value));
            } else {
                $array[] = $this->evaluate($item->value);
            }
        }
        return $array;
    }

    /** @return mixed */
    private function evaluateTernary(Expr\Ternary $expr) {
        if (null === $expr->if) {
            return $this->evaluate($expr->cond) ?: $this->evaluate($expr->else);
        }

        return $this->evaluate($expr->cond)
            ? $this->evaluate($expr->if)
            : $this->evaluate($expr->else);
    }

    /** @return mixed */
    private function evaluateBinaryOp(Expr\BinaryOp $expr) {
	    if ($expr instanceof Expr\BinaryOp\Coalesce) {
		    try {
			    $var = $this->evaluate($expr->left);
			    return $var ?? $this->evaluate($expr->right);
		    } catch(\Throwable $t){
			    //handle when isset($expr->left->var)===false
				return $this->evaluate($expr->right);
            }
        }

        // The evaluate() calls are repeated in each branch, because some of the operators are
        // short-circuiting and evaluating the RHS in advance may be illegal in that case
        $l = $expr->left;
        $r = $expr->right;
        switch ($expr->getOperatorSigil()) {
            case '&':   return $this->evaluate($l) &   $this->evaluate($r);
            case '|':   return $this->evaluate($l) |   $this->evaluate($r);
            case '^':   return $this->evaluate($l) ^   $this->evaluate($r);
            case '&&':  return $this->evaluate($l) &&  $this->evaluate($r);
            case '||':  return $this->evaluate($l) ||  $this->evaluate($r);
            case '??':  return $this->evaluate($l) ??  $this->evaluate($r);
            case '.':   return $this->evaluate($l) .   $this->evaluate($r);
            case '/':   return $this->evaluate($l) /   $this->evaluate($r);
            case '==':  return $this->evaluate($l) ==  $this->evaluate($r);
            case '>':   return $this->evaluate($l) >   $this->evaluate($r);
            case '>=':  return $this->evaluate($l) >=  $this->evaluate($r);
            case '===': return $this->evaluate($l) === $this->evaluate($r);
            case 'and': return $this->evaluate($l) and $this->evaluate($r);
            case 'or':  return $this->evaluate($l) or  $this->evaluate($r);
            case 'xor': return $this->evaluate($l) xor $this->evaluate($r);
            case '-':   return $this->evaluate($l) -   $this->evaluate($r);
            case '%':   return $this->evaluate($l) %   $this->evaluate($r);
            case '*':   return $this->evaluate($l) *   $this->evaluate($r);
            case '!=':  return $this->evaluate($l) !=  $this->evaluate($r);
            case '!==': return $this->evaluate($l) !== $this->evaluate($r);
            case '+':   return $this->evaluate($l) +   $this->evaluate($r);
            case '**':  return $this->evaluate($l) **  $this->evaluate($r);
            case '<<':  return $this->evaluate($l) <<  $this->evaluate($r);
            case '>>':  return $this->evaluate($l) >>  $this->evaluate($r);
            case '<':   return $this->evaluate($l) <   $this->evaluate($r);
            case '<=':  return $this->evaluate($l) <=  $this->evaluate($r);
            case '<=>': return $this->evaluate($l) <=> $this->evaluate($r);
            case '|>':
                $lval = $this->evaluate($l);
                return $this->evaluate($r)($lval);
        }

        throw new \Exception('Should not happen');
    }

    /** @return mixed */
    private function evaluateConstFetch(Expr\ConstFetch $expr) {
	    try {
			$name = $expr->name;
			if(! is_string($name)){
				//PHP_VERSION_ID usecase
				$name = $name->name;
	        }

		    if (defined($name)){
			    return constant($name);
		    }
		} catch(\Throwable $t){}

        return ($this->fallbackEvaluator)($expr);
    }

	/** @return mixed */
	private function evaluateIsset(Expr\Isset_ $expr) {
		try {
			foreach ($expr->vars as $var){
				$var = $this->evaluate($var);
				if (! isset($var)){
					return false;
				}
			}

			return true;
		} catch(\Throwable $t){
			return false;
		}
	}

	/** @return mixed */
	private function evaluateClassConstFetch(Expr\ClassConstFetch $expr) {
		try {
			$classname = $expr->class->name;
			$property = $expr->name->name;

			if ('class' === $property){
				return $classname;
			}

			if (class_exists($classname)){
				$class = new \ReflectionClass($classname);
				if (array_key_exists($property, $class->getConstants())) {
					$oReflectionConstant = $class->getReflectionConstant($property);
					if ($oReflectionConstant->isPublic()){
						return $class->getConstant($property);
					}
				}
			}
		} catch(\Throwable $t){}

		return ($this->fallbackEvaluator)($expr);
	}

	/** @return mixed */
	private function evaluateCast(Expr\Cast $expr) {
		try {
			$subexpr = $this->evaluate($expr->expr);
			$type = get_class($expr);
			switch ($type){
				case Expr\Cast\Array_::class:
					return (array) $subexpr;

				case Expr\Cast\Bool_::class:
					return (bool) $subexpr;

				case Expr\Cast\Double::class:
					switch ($expr->getAttribute("kind")){
						case Expr\Cast\Double::KIND_DOUBLE:
							return (double) $subexpr;

						case Expr\Cast\Double::KIND_FLOAT:
						case Expr\Cast\Double::KIND_REAL:
							return (float) $subexpr;
					}

					break;

				case Expr\Cast\Int_::class:
					return (int) $subexpr;

				case Expr\Cast\Object_::class:
					return (object) $subexpr;

				case Expr\Cast\String_::class:
					return (string) $subexpr;
			}
		} catch(\Throwable $t){
		}

		return ($this->fallbackEvaluator)($expr);
	}

	/** @return mixed */
	private function evaluateStaticPropertyFetch(Expr\StaticPropertyFetch $expr)
	{
		try {
			$classname = $expr->class->name;
			if ($expr->name instanceof Identifier){
				$property = $expr->name->name;
			} else {
				$property = $this->evaluate($expr->name);
			}

			if (class_exists($classname)){
				$class = new \ReflectionClass($classname);
				if (array_key_exists($property, $class->getStaticProperties())) {
					$oReflectionProperty = $class->getProperty($property);
					if ($oReflectionProperty->isPublic()){
						return $class->getStaticPropertyValue($property);
					}
				}
			}
		}
		catch (\Throwable $t) {}

		return ($this->fallbackEvaluator)($expr);
	}

	/** @return mixed */
	private function evaluateFuncCall(Expr\FuncCall $expr)
	{
		try {
			$name = $expr->name;
			if ($name instanceof Name){
				$function = $name->name;
			} else {
				$function = $this->evaluate($name);
			}

			if (! in_array($function, $this->functionsWhiteList)){
				throw new Exception("FuncCall $function not supported");
			}

			$args=[];
			foreach ($expr->args as $arg){
				/** @var \PhpParser\Node\Arg $arg */
				$args[]=$arg->value->value;
			}

			$reflection_function = new \ReflectionFunction($function);
			return $reflection_function->invoke(...$args);
		}
		catch (\Throwable $t) {}

		return ($this->fallbackEvaluator)($expr);
	}

	/** @return mixed */
	private function evaluateVariable(Expr\Variable $expr)
	{
		try {
			$name = $expr->name;
			if (array_key_exists($name, get_defined_vars())) {
				return $$name;
			}

			if (array_key_exists($name, $GLOBALS)) {
				global $$name;
				return $$name;
			}
		} catch (\Throwable $t) {
		}

		return ($this->fallbackEvaluator)($expr);
	}

	/** @return mixed */
	private function evaluateStaticCall(Expr\StaticCall $expr)
	{
		try {
			$class = $expr->class->name;
			if ($expr->name instanceof Identifier){
				$method = $expr->name->name;
			} else {
				$method = $this->evaluate($expr->name);
			}

			$static_call_description = "$class::$method";
			if (! in_array($static_call_description, $this->staticCallsWhitelist)){
				throw new Exception("StaticCall $static_call_description not supported");
			}

			$args=[];
			foreach ($expr->args as $arg){
				/** @var \PhpParser\Node\Arg $arg */
				$args[]=$arg->value->value;
			}

			$class = new \ReflectionClass($class);
			$method = $class->getMethod($method);
			if ($method->isPublic()){
				return $method->invokeArgs(null, $args);
			}
		} catch (\Throwable $t) {}

		return ($this->fallbackEvaluator)($expr);
	}

	/**
	 * @param \PhpParser\Node\Expr\NullsafePropertyFetch|\PhpParser\Node\Expr\PropertyFetch $expr
	 *
	 * @return mixed
	 */
	private function evaluatePropertyFetch($expr)
	{
		try {
			$var = $this->evaluate($expr->var);
		} catch (\Throwable $t) {
			$var = null;
		}

		if (! is_null($var)) {
			try {
				if ($expr->name instanceof Identifier) {
					$name = $expr->name->name;
				} else {
					$name = $this->evaluate($expr->name);
				}

				$reflectionClass = new \ReflectionClass(get_class($var));
				$property = $reflectionClass->getProperty($name);
				if ($property->isPublic()) {
					return $property->getValue($var);
				}
			}
			catch (\Throwable $t) {}
		} else if ($expr instanceof Expr\NullsafePropertyFetch){
			return null;
		}

		return ($this->fallbackEvaluator)($expr);
	}

	/**
	 * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\NullsafeMethodCall $expr
	 *
	 * @return mixed
	 */
	private function evaluateMethodCall($expr)
	{
		try {
			$var = $this->evaluate($expr->var);
		} catch (\Throwable $t) {
			$var = null;
		}

		if (! is_null($var)) {
			try {
				$args = [];
				foreach ($expr->args as $arg) {
				/** @var \PhpParser\Node\Arg $arg */
					$args[] = $arg->value->value;
			}

			if ($expr->name instanceof Identifier) {
				$name = $expr->name->name;
			} else {
				$name = $this->evaluate($expr->name);
			}

			$reflectionClass = new \ReflectionClass(get_class($var));
			$method = $reflectionClass->getMethod($name);
				if ($method->isPublic()) {
					return $method->invokeArgs($var, $args);
				}
			}
			catch (\Throwable $t) {}
		} else if ($expr instanceof Expr\NullsafeMethodCall){
			return null;
		}

		return ($this->fallbackEvaluator)($expr);
	}
}
