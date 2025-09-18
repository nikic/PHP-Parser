<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Exception;

use function array_merge;

/**
 * Evaluates any expressions.
 *
 * To handle not-supported expressions you need, please provide your own fallback evaluator
 *
 * The fallback evaluator should throw ExprEvaluationException for nodes it cannot evaluate.
 *
 * Method/function evaluation relies on white lists. Please specify which ones are allowed to be evaluated/called before evaluation
 * (@see setStaticCallsWhitelist/ @see setFunctionsWhitelist.
 */
class ExprEvaluator extends ConstExprEvaluator {
    /** @var callable|null */
    protected $fallbackEvaluator;

    /** @var array<string> */
    private array $functionsWhiteList = [];

    /** @var array<string> */
    private array $staticCallsWhitelist = [];

    /**
     * Create a constant expression evaluator.
     *
     * The provided fallback evaluator is invoked whenever a subexpression cannot be evaluated. See
     * class doc comment for more information.
     *
     * @param callable|null $fallbackEvaluator To call if subexpression cannot be evaluated
     */
    public function __construct(?callable $fallbackEvaluator = null) {
        parent::__construct($fallbackEvaluator);

        $this->fallbackEvaluator = $fallbackEvaluator ?? function (Expr $expr) {
            throw new ExprEvaluationException(
                "Expression of type {$expr->getType()} cannot be evaluated"
            );
        };
    }

    /**
     * @param array<string> $functionsWhiteList
     */
    public function setFunctionsWhitelist(array $functionsWhiteList): void {
        $this->functionsWhiteList = $functionsWhiteList;
    }

    /**
     * @param array<string> $staticCallsWhitelist
     */
    public function setStaticCallsWhitelist(array $staticCallsWhitelist): void {
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
     *
     * @return mixed Result of evaluation
     *
     * @throws ExprEvaluationException if the expression cannot be evaluated or an error occurred
     */
    public function evaluateSilently(Expr $expr) {
        set_error_handler(function ($num, $str, $file, $line) {
            throw new \ErrorException($str, 0, $num, $file, $line);
        });

        try {
            return $this->evaluate($expr);
        } catch (\Throwable $e) {
            if (!$e instanceof ExprEvaluationException) {
                $e = new ExprEvaluationException(
                    "An error occurred during expression evaluation", 0, $e);
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
     *
     * @return mixed Result of evaluation
     *
     * @throws ExprEvaluationException if the expression cannot be evaluated
     */
    public function evaluateDirectly(Expr $expr) {
        return $this->evaluate($expr);
    }

    /** @return mixed */
    protected function evaluate(Expr $expr) {
        try {
            return parent::evaluate($expr);
        } catch (\Throwable $t) {
        }

        if ($expr instanceof Expr\Variable) {
            return $this->evaluateVariable($expr);
        }

        if ($expr instanceof Expr\BinaryOp\Coalesce) {
            try {
                $var = $this->evaluate($expr->left);
            } catch (\Throwable $t) {
                //left expression cannot be evaluated (! isset for exeample)
                return $this->evaluate($expr->right);
            }

            return $var ?? $this->evaluate($expr->right);
        }

        if ($expr instanceof Expr\Isset_) {
            return $this->evaluateIsset($expr);
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

        if ($expr instanceof Expr\NullsafePropertyFetch || $expr instanceof Expr\PropertyFetch) {
            return $this->evaluatePropertyFetch($expr);
        }

        if ($expr instanceof Expr\NullsafeMethodCall || $expr instanceof Expr\MethodCall) {
            return $this->evaluateMethodCall($expr);
        }

        return ($this->fallbackEvaluator)($expr);
    }

    /** @return bool */
    private function evaluateIsset(Expr\Isset_ $expr) {
        try {
            foreach ($expr->vars as $var) {
                $var = $this->evaluate($var);
                if (! isset($var)) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $t) {
            return false;
        }
    }

    /** @return mixed */
    private function evaluateStaticPropertyFetch(Expr\StaticPropertyFetch $expr) {
        try {
            $classname = $expr->class->name;
            if ($expr->name instanceof Identifier) {
                $property = $expr->name->name;
            } else {
                $property = $this->evaluate($expr->name);
            }

            if (class_exists($classname)) {
                $class = new \ReflectionClass($classname);
                if (array_key_exists($property, $class->getStaticProperties())) {
                    $oReflectionProperty = $class->getProperty($property);
                    if ($oReflectionProperty->isPublic()) {
                        return $class->getStaticPropertyValue($property);
                    }
                }
            }
        } catch (\Throwable $t) {
        }

        return ($this->fallbackEvaluator)($expr);
    }

    /** @return mixed */
    private function evaluateFuncCall(Expr\FuncCall $expr) {
        try {
            $name = $expr->name;
            if ($name instanceof Name) {
                $function = $name->name;
            } else {
                $function = $this->evaluate($name);
            }

            if (! in_array($function, $this->functionsWhiteList)) {
                throw new Exception("FuncCall $function not supported");
            }

            $args = [];
            foreach ($expr->args as $arg) {
                /** @var \PhpParser\Node\Arg $arg */
                $args[] = $this->evaluate($arg->value);
            }

            $reflection_function = new \ReflectionFunction($function);
            return $reflection_function->invoke(...$args);
        } catch (\Throwable $t) {
        }

        return ($this->fallbackEvaluator)($expr);
    }

    /** @return mixed */
    private function evaluateVariable(Expr\Variable $expr) {
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
    private function evaluateStaticCall(Expr\StaticCall $expr) {
        try {
            $class = $expr->class->name;
            if ($expr->name instanceof Identifier) {
                $method = $expr->name->name;
            } else {
                $method = $this->evaluate($expr->name);
            }

            $static_call_description = "$class::$method";
            if (! in_array($static_call_description, $this->staticCallsWhitelist)) {
                throw new Exception("StaticCall $static_call_description not supported");
            }

            $args = [];
            foreach ($expr->args as $arg) {
                /** @var \PhpParser\Node\Arg $arg */
                $args[] = $this->evaluate($arg->value);
            }

            $class = new \ReflectionClass($class);
            $method = $class->getMethod($method);
            if ($method->isPublic()) {
                return $method->invokeArgs(null, $args);
            }
        } catch (\Throwable $t) {
        }

        return ($this->fallbackEvaluator)($expr);
    }

    /**
     * @param \PhpParser\Node\Expr\NullsafePropertyFetch|\PhpParser\Node\Expr\PropertyFetch $expr
     *
     * @return mixed
     */
    private function evaluatePropertyFetch($expr) {
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
            } catch (\Throwable $t) {
            }
        } elseif ($expr instanceof Expr\NullsafePropertyFetch) {
            return null;
        }

        return ($this->fallbackEvaluator)($expr);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\NullsafeMethodCall $expr
     *
     * @return mixed
     */
    private function evaluateMethodCall($expr) {
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
                    $args[] = $this->evaluate($arg->value);
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
            } catch (\Throwable $t) {
            }
        } elseif ($expr instanceof Expr\NullsafeMethodCall) {
            return null;
        }

        return ($this->fallbackEvaluator)($expr);
    }
}
