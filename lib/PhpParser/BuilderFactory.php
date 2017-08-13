<?php

namespace PhpParser;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Use_;

/**
 * The following methods use reserved keywords, so their implementation is defined with an underscore and made available
 * with the reserved name through __call() magic.
 *
 * @method Builder\Namespace_ namespace(string $name) Creates a namespace builder.
 * @method Builder\Class_     class(string $name)     Creates a class builder.
 * @method Builder\Interface_ interface(string $name) Creates an interface builder.
 * @method Builder\Trait_     trait(string $name)     Creates a trait builder.
 * @method Builder\Function_  function(string $name)  Creates a function builder.
 * @method Builder\Use_       use(string $name)       Creates a namespace/class use builder.
 */
class BuilderFactory
{
    /**
     * Creates a namespace builder.
     *
     * @param null|string|Node\Name $name Name of the namespace
     *
     * @return Builder\Namespace_ The created namespace builder
     */
    protected function _namespace($name) : Builder\Namespace_ {
        return new Builder\Namespace_($name);
    }

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     *
     * @return Builder\Class_ The created class builder
     */
    protected function _class(string $name) : Builder\Class_ {
        return new Builder\Class_($name);
    }

    /**
     * Creates an interface builder.
     *
     * @param string $name Name of the interface
     *
     * @return Builder\Interface_ The created interface builder
     */
    protected function _interface(string $name) : Builder\Interface_ {
        return new Builder\Interface_($name);
    }

    /**
     * Creates a trait builder.
     *
     * @param string $name Name of the trait
     *
     * @return Builder\Trait_ The created trait builder
     */
    protected function _trait(string $name) : Builder\Trait_ {
        return new Builder\Trait_($name);
    }

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     *
     * @return Builder\Method The created method builder
     */
    public function method(string $name) : Builder\Method {
        return new Builder\Method($name);
    }

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     *
     * @return Builder\Param The created parameter builder
     */
    public function param(string $name) : Builder\Param {
        return new Builder\Param($name);
    }

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     *
     * @return Builder\Property The created property builder
     */
    public function property(string $name) : Builder\Property {
        return new Builder\Property($name);
    }

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     *
     * @return Builder\Function_ The created function builder
     */
    protected function _function(string $name) : Builder\Function_ {
        return new Builder\Function_($name);
    }

    /**
     * Creates a namespace/class use builder.
     *
     * @param string|Node\Name Name to alias
     *
     * @return Builder\Use_ The create use builder
     */
    protected function _use($name) : Builder\Use_ {
        return new Builder\Use_($name, Use_::TYPE_NORMAL);
    }

    /**
     * Creates node a for a literal value.
     *
     * @param Expr|bool|null|int|float|string|array $value $value
     *
     * @return Expr
     */
    public function val($value) : Expr {
        return BuilderHelpers::normalizeValue($value);
    }

    /**
     * Normalizes an argument list.
     *
     * Creates Arg nodes for all arguments and converts literal values to expressions.
     *
     * @param array $args List of arguments to normalize
     *
     * @return Arg[]
     */
    public function args(array $args) : array {
        $normalizedArgs = [];
        foreach ($args as $arg) {
            if ($arg instanceof Arg) {
                $normalizedArgs[] = $arg;
            } else {
                $normalizedArgs[] = new Arg(BuilderHelpers::normalizeValue($arg));
            }
        }
        return $normalizedArgs;
    }

    /**
     * Creates nested Concat nodes from a list of expressions.
     *
     * @param Expr|string ...$exprs Expressions or literal strings
     *
     * @return Concat
     */
    public function concat(...$exprs) : Concat {
        $numExprs = count($exprs);
        if ($numExprs < 2) {
            throw new \LogicException('Expected at least two expressions');
        }

        $lastConcat = $this->normalizeStringExpr($exprs[0]);
        for ($i = 1; $i < $numExprs; $i++) {
            $lastConcat = new Concat($lastConcat, $this->normalizeStringExpr($exprs[$i]));
        }
        return $lastConcat;
    }

    public function __call($name, array $args) {
        if (method_exists($this, '_' . $name)) {
            return $this->{'_' . $name}(...$args);
        }

        throw new \LogicException(sprintf('Method "%s" does not exist', $name));
    }

    private function normalizeStringExpr($expr) {
        if ($expr instanceof Expr) {
            return $expr;
        }

        if (is_string($expr)) {
            return new String_($expr);
        }

        throw new \LogicException('Expected string or Expr');
    }
}
