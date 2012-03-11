Code generation
===============

...

Builders
--------

The project provides builders for classes, methods, functions, parameters and properties, which
allow creating node trees with a fluid interface, instead of instantiating all nodes manually.

Here is an example:

```php
<?php
$factory = new PHPParser_BuilderFactory;
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
        ->addStmt(new PHPParser_Node_Expr_Print(new PHPParser_Node_Expr_Variable('someParam')))
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