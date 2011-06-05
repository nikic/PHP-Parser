PHP Parser
==========

This is a PHP parser written in PHP. It's purpose is to simplify static code analysis and
manipulation.

***Note: This project is highly experimental. It may not always function correctly.***

Components
==========

This package currently bundles several components:

 * The `Parser` itself
 * A `NodeDumper` to dump the nodes to a human readable string representation
 * A `PrettyPrinter` to translate the node tree back to PHP

Autoloader
----------

In order to automatically include required files `PHPParser_Autoloader` can be used:

    require_once 'path/to/phpparser/lib/PHPParser/Autoloader.php';
    PHPParser_Autoloader::register();

Parser and ParserDebug
----------------------

Parsing is performed using `PHPParser_Parser->parse()`. This method accepts a `PHPParser_Lexer`
as the only parameter and returns an array of statement nodes. If an error occurs it throws a
PHPParser_Error.

    $code = '<?php // some code';

    try {
        $parser = new PHPParser_Parser;
        $stmts = $parser->parse(new PHPParser_Lexer($code));
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

The `PHPParser_ParserDebug` class also parses a PHP code, but outputs a debug trace while doing so.

Node Tree
---------

The output of the parser is an array of statement nodes. All nodes are instances of
`PHPParser_NodeAbstract`. Furthermore nodes are divided into three categories:

 * `PHPParser_Node_Stmt`: A statement
 * `PHPParser_Node_Expr`: An expression
 * `PHPParser_Node_Scalar`: A scalar (which is a string, a number, aso.)
   `PHPParser_Node_Scalar` inherits from `PHPParser_Node_Expr`.

Each node may have subnodes. For example `PHPParser_Node_Expr_Plus` has two subnodes, namely `left`
and `right`, which represend the left hand side and right hand side expressions of the plus operation.
Subnodes are accessed as normal properties:

    $node->left

The subnodes which a certain node can have are documented as `@property` doccomments in the
respective files.

NodeDumper
----------

Nodes can be dumped into a string representation using the `PHPParser_NodeDumper->dump()` method:

    $code = <<<'CODE'
    <?php
        function printLine($msg) {
            echo $msg, "\n";
        }

        printLine('Hallo World!!!');
    CODE;

    try {
        $parser = new PHPParser_Parser;
        $stmts = $parser->parse(new PHPParser_Lexer($code));

        $nodeDumper = new PHPParser_NodeDumper;
        echo '<pre>' . htmlspecialchars($nodeDumper->dump($stmts)) . '</pre>';
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

This script will have an output similar to the following:

    array(
        0: Stmt_Func(
            byRef: false
            name: printLine
            params: array(
                0: Stmt_FuncParam(
                    type: null
                    name: msg
                    byRef: false
                    default: null
                )
            )
            stmts: array(
                0: Stmt_Echo(
                    exprs: array(
                        0: Variable(
                            name: msg
                        )
                        1: Scalar_String(
                            value:

                            isBinary: false
                            type: 1
                        )
                    )
                )
            )
        )
        1: Expr_FuncCall(
            func: Name(
                parts: array(
                    0: printLine
                )
            )
            args: array(
                0: Expr_FuncCallArg(
                    value: Scalar_String(
                        value: Hallo World!!!
                        isBinary: false
                        type: 0
                    )
                    byRef: false
                )
            )
        )
    )

PrettyPrinter
-------------

The pretty printer compiles nodes back to PHP code. "Pretty printing" here is just the formal
name of the process and does not mean that the output is in any way pretty.

    $prettyPrinter = new PHPParser_PrettyPrinter_Zend;
    echo '<pre>' . htmlspecialchars($prettyPrinter->prettyPrint($stmts)) . '</pre>';

For the code mentioned in the above section this should create the output:

    function printLine($msg)
    {
        echo $msg, "\n";
    }
    printLine('Hallo World!!!');