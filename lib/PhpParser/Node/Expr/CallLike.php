<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\VariadicPlaceholder;

abstract class CallLike extends Expr {
    /**
     * Return raw arguments, which may be actual Args, or VariadicPlaceholders for first-class
     * callables.
     *
     * @return array<Arg|VariadicPlaceholder>
     */
    abstract public function getRawArgs(): array;

    /**
     * Returns whether this call expression is actually a first class callable.
     */
    public function isFirstClassCallable(): bool {
        $rawArgs = $this->getRawArgs();
        return count($rawArgs) === 1 && current($rawArgs) instanceof VariadicPlaceholder;
    }

    /**
     * Returns whether this call expression is a partial function application, i.e. whether its
     * argument list contains one or more "?" placeholders or a "..." placeholder. First-class
     * callables are a special case of partial function application, so this also returns true
     * for them.
     */
    public function isPartialFunctionApplication(): bool {
        foreach ($this->getRawArgs() as $arg) {
            if ($arg instanceof VariadicPlaceholder || $arg->value instanceof Placeholder) {
                return true;
            }
        }
        return false;
    }

    /**
     * Assert that this is not a partial function application (which includes first-class
     * callables) and return only ordinary Args.
     *
     * @return Arg[]
     */
    public function getArgs(): array {
        assert(!$this->isPartialFunctionApplication());
        return $this->getRawArgs();
    }

    /**
     * Retrieves a specific argument from the raw arguments.
     *
     * Returns the named argument that matches the given `$name`, or the
     * positional (unnamed) argument that exists at the given `$position`.
     * Returns `null` if no match is found, or when a "..." placeholder is
     * encountered, as argument positions are no longer known past that point.
     */
    public function getArg(string $name, int $position): ?Arg {
        foreach ($this->getRawArgs() as $i => $arg) {
            if ($arg instanceof VariadicPlaceholder) {
                return null;
            }
            if ($arg->unpack) {
                continue;
            }
            if (
                ($arg->name !== null && $arg->name->toString() === $name)
                || ($arg->name === null && $i === $position)
            ) {
                return $arg;
            }
        }
        return null;
    }
}
