AST builders
============

When PHP-Parser is used to generate (or modify) code by first creating an Abstract Syntax Tree and
then using the [pretty printer](Pretty_printing.markdown) to convert it to PHP code, it can often
be tedious to manually construct AST nodes. The project provides a number of utilities to simplify
the construction of common AST nodes.

Fluent builders
---------------

The library comes with a number of builders, which allow creating node trees using a fluent
interface. Builders are created using the `BuilderFactory` and the final constructed node is
accessed through `getNode()`. Fluent builders are available for
the following syntactic elements:

 * namespaces and use statements
 * classes, interfaces and traits
 * methods, functions and parameters
 * properties

Here is an example:

```php
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;

$factory = new BuilderFactory;
$node = $factory->namespace('Name\Space')
    ->addStmt($factory->use('Some\Other\Thingy')->as('SomeOtherClass'))
    ->addStmt($factory->class('SomeOtherClass')
        ->extend('SomeClass')
        ->implement('A\Few', '\Interfaces')
        ->makeAbstract() // ->makeFinal()

        ->addStmt($factory->method('someMethod')
            ->makePublic()
            ->makeAbstract() // ->makeFinal()
            ->setReturnType('bool')
            ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
            ->setDocComment('/**
                              * This method does something.
                              *
                              * @param SomeClass And takes a parameter
                              */')
        )

        ->addStmt($factory->method('anotherMethod')
            ->makeProtected() // ->makePublic() [default], ->makePrivate()
            ->addParam($factory->param('someParam')->setDefault('test'))
            // it is possible to add manually created nodes
            ->addStmt(new Node\Expr\Print_(new Node\Expr\Variable('someParam')))
        )

        // properties will be correctly reordered above the methods
        ->addStmt($factory->property('someProperty')->makeProtected())
        ->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault(array(1, 2, 3)))
    )

    ->getNode()
;

$stmts = array($node);
$prettyPrinter = new PrettyPrinter\Standard();
echo $prettyPrinter->prettyPrintFile($stmts);
```

This will produce the following output with the standard pretty printer:

```php
<?php

namespace Name\Space;

use Some\Other\Thingy as SomeClass;
abstract class SomeOtherClass extends SomeClass implements A\Few, \Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam) : bool;
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
```

Additional helper methods
-------------------------

The `BuilderFactory` also provides a number of additional helper methods, which directly return
nodes. The following methods are currently available:

 * `val($value)`: Creates an AST node for a literal value like `42` or `[1, 2, 3]`.
 * `args(array $args)`: Creates an array of function/method arguments, including the required `Arg`
   wrappers. Also converts literals to AST nodes.
 * `concat(...$exprs)`: Create a tree of `BinaryOp\Concat` nodes for the given expressions.

These methods may be expanded on an as-needed basis. Please open an issue or PR if a common
operation is missing.