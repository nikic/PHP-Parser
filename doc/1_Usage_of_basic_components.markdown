Usage of basic components
=========================

This document explains how to use the parser, the pretty printer and the node traverser.

Bootstrapping
-------------

The library needs to register a class autoloader; you can do this either by including the
`bootstrap.php` file:

    require 'path/to/PHP-Parser/lib/bootstrap.php';

Or by manually registering the loader:

    require 'path/to/PHP-Parser/lib/PHPParser/Autoloader.php';
    PHPParser_Autoloader::register();

Parsing
-------

Parsing is done by calling the `parse` method of a `PHPParser_Parser` object. The method
expects a `PHPParser_Lexer` instance which itself again expects a PHP source code (including
`<?php` opening tags). If a syntax error is encountered `PHPParser_Error` is thrown, so
this exception should be `catch`ed.

    $code = '<?php // some code';

    $parser = new PHPParser_Parser;

    try {
        $stmts = $parser->parse(new PHPParser_Lexer($code));
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

The `parse` method will return an array of statement nodes (`$stmts`).

Node tree
---------

If you use the above code with `$code = "<?php echo 'Hi ', hi\\getTarget();"` the parser will
generate a node tree looking like this:

    array(
        0: Stmt_Echo(
            exprs: array(
                0: Scalar_String(
                    value: Hi
                )
                1: Expr_FuncCall(
                    name: Name(
                        parts: array(
                            0: hi
                            1: getTarget
                        )
                    )
                    args: array(
                    )
                )
            )
        )
    )

Thus `$stmts` will contain an array with only one node, with this node being an instance of
`PHPParser_Node_Stmt_Echo`.

As PHP is a large language there are approximately 140 different nodes. In order to make work
with them easier they are grouped into three categories:

 * `PHPParser_Node_Stmt`s are statement nodes, i.e. language constructs that do not return
   a value and can not occur in an expression. For example a class definition is a statement.
   It doesn't return a value and you can't write something like `func(class A {});`.
 * `PHPParser_Node_Expr`s are expression nodes, i.e. language constructs that return a value
   and thus can occur in other expressions. Examples of expressions are `$var`
   (`PHPParser_Node_Expr_Variable`) and `func()` (`PHPParser_Node_Expr_FuncCall`).
 * `PHPParser_Node_Scalar`s are nodes representing scalar values, like `'string'`
   (`PHPParser_Node_Scalar_String`), `0` (`PHPParser_Node_Scalar_LNumber`) or magic constants
   like `__FILE__` (`PHPParser_Node_Scalar_FileConst`). All `PHPParser_Node_Scalar`s extend
   `PHPParser_Node_Expr`, as scalars are expressions, too.
 * There are some nodes not in either of these groups, for example names (`PHPParser_Node_Name`)
   and call arguments (`PHPParser_Node_Arg`).

Every node has a (possibly zero) number of subnodes. You can access subnodes by writing
`$node->subNodeName`. The `Stmt_Echo` node has only one subnode `exprs`. So in order to access it
in the above example you would write `$stmts[0]->exprs`. If you wanted to access name of the function
call, you would write `$stmts[0]->exprs[1]->name`.

All nodes also define a `getType()` method that returns the node type (the type is the class name
without the `PHPParser_Node_` prefix). Additionally there are `getLine()`, which returns the line
the node startet in, and `getDocComment()`, which returns the doc comment above the node (if there
is any), and the respective setters `setLine()` and `setDocComment()`.

Pretty printer
--------------

The pretty printer component compiles the AST back to PHP code. As the parser does not retain formatting
information the formatting is done using a specified scheme. Currently there is only one scheme available,
namely `PHPParser_PrettyPrinter_Zend` (the name "Zend" might be misleading. It does not strictly adhere
to the Zend Coding Standard.)

    $code = "<?php echo 'Hi ', hi\\getTarget();";

    $parser        = new PHPParser_Parser;
    $prettyPrinter = new PHPParser_PrettyPrinter_Zend;

    try {
        // parse
        $stmts = $parser->parse(new PHPParser_Lexer($code));

        // change
        $stmts[0]         // the echo statement
              ->exprs     // sub expressions
              [0]         // the first of them (the string node)
              ->value     // it's value, i.e. 'Hi '
              = 'Hallo '; // change to 'Hallo '

        // pretty print
        $code = '<?php ' . $prettyPrinter->prettyPrint($stmts);

        echo $code;
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

The above code will output:

    <?php echo 'Hallo ', hi\getTarget();

As you can see the source code was first parsed using `PHPParser_Parser->parse`, then changed and then
again converted to code using `PHPParser_PrettyPrinter_Zend->prettyPrint`.

The `prettyPrint` method pretty prints a statements array. It is also possible to pretty print only a
single expression using `prettyPrintExpr`.

Node traversation
-----------------

The above pretty printing example used the fact that the source code was known and thus it was easy to
write code that accesses a certain part of a node tree and changes it. Normally this is not the case.
Usually you want to change / analyze code in a generic way, where you don't know how the node tree is
going to look like.

For this purpose the parser provides a component for traversing and visiting the node tree. The basic
structure of a program using this `PHPParser_NodeTraverser` looks like this:

    $code = "<?php // some code";

    $parser        = new PHPParser_Parser;
    $traverser     = new PHPParser_NodeTraverser;
    $prettyPrinter = new PHPParser_PrettyPrinter_Zend;

    // add your visitor
    $traverser->addVisitor(new MyNodeVisitor);

    try {
        // parse
        $stmts = $parser->parse(new PHPParser_Lexer($code));

        // traverse
        $stmts = $traverser->traverse($stmts);

        // pretty print
        $code = '<?php ' . $prettyPrinter->prettyPrint($stmts);

        echo $code;
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

A same node visitor for this code might look like this:

    class MyNodeVisitor extends PHPParser_NodeVisitorAbstract
    {
        public function leaveNode(PHPParser_Node $node) {
            if ($node instanceof PHPParser_Node_Scalar_String) {
                $node->value = 'foo';
            }
        }
    }

The above node visitor would change all string literals in the program to `'foo'`.

All visitors must implement the `PHPParser_NodeVisitor` interface, which defined the following four
methods:

    public function beforeTraverse(array $nodes);
    public function enterNode(PHPParser_Node $node);
    public function leaveNode(PHPParser_Node $node);
    public function afterTraverse(array $nodes);

The `beforeTraverse` method is called once before the traversal begins and is passed the nodes the
traverser was called with. This method can be used for resetting values before traversation or
preparing the tree for traversal.

The `afterTraverse` method is similar to the `beforeTraverse` method, with the only difference that
it is called once after the traversal.

The `enterNode` and `leaveNode` methods are called on every node, the former when it is entered,
i.e. before its subnodes are traversed, the latter when it is left.

All four methods can either return the changed node or not return at all (i.e. `null`) in which
case the current node is not changed. The `leaveNode` method can furthermore return two special
values: If `false` is returned the current node will be removed from the parent array. If an `array`
is returned the current node will be merged into the parent array at the offset of the current node.
I.e. if in `array(A, B, C)` the node `B` should be replaced with `array(X, Y, Z)` the result will be
`array(A, X, Y, Z, C)`.

Instead of manually implementing the `NodeVisitor` interface you can also extend the `NodeVisitorAbstract`
class, which will define empty default implementations for all the above methods.

The NameResolver node visitor
-----------------------------

One visitor is already bundled with the package: `PHPParser_NodeVisitor_NameResolver`. This visitor
helps you work with namespaced code by trying to resolve most names to fully qualified ones.

For example, consider the following code:

    use A as B;
    new B\C();

In order to know that `B\C` really is `A\C` you would need to track aliases and namespaces yourself.
The `NameResolver` takes care of that and resolves names as far as possible.

After running it most names will be fully qualified. The only names that will stay unqualified are
unqualified function and constant names. These are resolved at runtime and thus the visitor can't
know which function they are referring to. In most cases this is a non-issue as the global functions
are meant.