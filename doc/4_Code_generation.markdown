Code generation
===============

It is also possible to generate code using the parser, by first creating an Abstract Syntax Tree and then using the
pretty printer to convert it to PHP code. To simplify code generation, the project comes with a set of builders for
classes, interfaces, methods, functions, parameters and properties. The builders allow creating node trees using a
fluid interface, instead of instantiating all nodes manually.

Here is an example:

```php
<?php
$factory = new PhpParser\BuilderFactory;
$node = $factory->class('SomeClass')
    ->extend('SomeOtherClass')
    ->implement('A\Few', 'Interfaces')
    ->makeAbstract() // ->makeFinal()

    ->addStmt($factory->method('someMethod')
        ->makeAbstract() // ->makeFinal()
        ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
    )

    ->addStmt($factory->method('anotherMethod')
        ->makeProtected() // ->makePublic() [default], ->makePrivate()
        ->addParam($factory->param('someParam')->setDefault('test'))
        // it is possible to add manually created nodes
        ->addStmt(new PhpParser\Node\Expr\Print_(new PhpParser\Node\Expr\Variable('someParam')))
    )

    // properties will be correctly reordered above the methods
    ->addStmt($factory->property('someProperty')->makeProtected())
    ->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault(array(1, 2, 3)))

    ->getNode()
;

$stmts = array($node);
echo $prettyPrinter->prettyPrint($stmts);
```

This will produce the following output with the default pretty printer:

```php
<?php
abstract class SomeClass extends SomeOtherClass implements A\Few, Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
```
