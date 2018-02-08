Other node tree representations
===============================

It is possible to convert the AST into several textual representations, which serve different uses.

Simple serialization
--------------------

It is possible to serialize the node tree using `serialize()` and also unserialize it using
`unserialize()`. The output is not human readable and not easily processable from anything
but PHP, but it is compact and generates quickly. The main application thus is in caching.

Human readable dumping
----------------------

Furthermore it is possible to dump nodes into a human readable format using the `dump` method of
`PhpParser\NodeDumper`. This can be used for debugging.

```php
$code = <<<'CODE'
<?php

function printLine($msg) {
    echo $msg, "\n";
}

printLine('Hello World!!!');
CODE;

$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
$nodeDumper = new PhpParser\NodeDumper;

try {
    $stmts = $parser->parse($code);

    echo $nodeDumper->dump($stmts), "\n";
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```

The above script will have an output looking roughly like this:

```
array(
    0: Stmt_Function(
        byRef: false
        params: array(
            0: Param(
                name: msg
                default: null
                type: null
                byRef: false
            )
        )
        stmts: array(
            0: Stmt_Echo(
                exprs: array(
                    0: Expr_Variable(
                        name: msg
                    )
                    1: Scalar_String(
                        value:

                    )
                )
            )
        )
        name: printLine
    )
    1: Expr_FuncCall(
        name: Name(
            parts: array(
                0: printLine
            )
        )
        args: array(
            0: Arg(
                value: Scalar_String(
                    value: Hello World!!!
                )
                byRef: false
            )
        )
    )
)
```

JSON encoding
-------------

See [JSON representation](component/JSON_representation.markdown) section.