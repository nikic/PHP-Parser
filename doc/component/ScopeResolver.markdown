ScopeResolver documentation
===========================

ScopeResolver is a default NodeVisitor for adding `scope` attributes to each of the nodes.

Having a scope attribute handily available for every statement can be really useful, for example, when trying to
figure out when a variable assignment goes out of scope.

Usage
-----

Using ScopeResolver is extremely simple. There are no options, just add ScopeResolver to NodeTraverser
and let it run. Here is an example:

```php
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ScopeResolver;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$traverser = new NodeTraverser;

$traverser->addVisitor(new ScopeResolver);

try {
    // read the file that should be converted
    $code = file_get_contents($file);

    // parse
    $stmts = $parser->parse($code);

    // traverse
    $stmts = $traverser->traverse($stmts);

    // see the results!
    var_dump($stmts);
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```

Now each of the nodes should have a `scope` attribute following the syntax form below.

Syntax
------

In case you require additional information about a scope, it is given to you in an easily parsable string format.

All scopes are prepended by `\`, which is the root namespace.

All namespaces (without anything inside) end with `\` to indicate a raw namespace.

Functions are appended by the function definition braces `()`.

Class methods are prepended by the object operator `->`.

Closures are essentially Closure classes, so they are treated as such in a namespace.  
If inside a class or a function, or if they are nested, they are prepended by the scope resolution operator `::`.

## Examples:

Root namespace: `\`  
Function `test()` inside the root namespace: `\test()`  
Raw `Vendor\Package\Tester` namespace: `\Vendor\Package\Tester\`  
Function `test()` inside the namespace: `\Vendor\Package\Tester\test()`  
Class `TestClass` inside the namespace `\Vendor\Package\Tester\TestClass`  
Class method `coverage()`: `\Vendor\Package\Tester\TestClass->coverage()`  
A Closure inside the root namespace: `\Closure`  
Nested Closures inside the root namespace: `\Closure::Closure::Closure`  
A Closure inside namespaced function `test()`: `\Vendor\Package\Tester\test()::Closure::Closure`  
A Closure inside namespaced class method `coverage()`: `\Vendor\Package\Tester\TestClass->coverage()::Closure`  