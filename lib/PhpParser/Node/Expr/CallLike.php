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
     * Return only ordinary Args.
     *
     * @return Arg[]
     */
    public function getArgs(): array {
        if ($this->isFirstClassCallable()) {
            return [];
        }
        return $this->getRawArgs();
    }

    /**
     * Retrieves a specific argument from the raw arguments.
     *
     * Returns the named argument that matches the given `$name`, or the
     * positional (unnamed) argument that exists at the given `$position`,
     * otherwise, returns `null` for first-class callables or if no match is found.
     */
    public function getArg(string $name, int $position): ?Arg {
        if ($this->isFirstClassCallable()) {
            return null;
        }
        foreach ($this->getRawArgs() as $i => $arg) {
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
