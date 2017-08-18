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

Nodes (and comments) implement the `JsonSerializable` interface. As such, it is possible to JSON
encode the AST directly using `json_encode()`:

```php
$code = <<<'CODE'
<?php

function printLine($msg) {
    echo $msg, "\n";
}

printLine('Hello World!!!');
CODE;

$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);

try {
    $stmts = $parser->parse($code);

    echo json_encode($stmts, JSON_PRETTY_PRINT), "\n";
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```

This will result in the following output (which includes attributes):

```json
[
    {
        "nodeType": "Stmt_Function",
        "byRef": false,
        "name": "printLine",
        "params": [
            {
                "nodeType": "Param",
                "type": null,
                "byRef": false,
                "variadic": false,
                "name": "msg",
                "default": null,
                "attributes": {
                    "startLine": 3,
                    "endLine": 3
                }
            }
        ],
        "returnType": null,
        "stmts": [
            {
                "nodeType": "Stmt_Echo",
                "exprs": [
                    {
                        "nodeType": "Expr_Variable",
                        "name": "msg",
                        "attributes": {
                            "startLine": 4,
                            "endLine": 4
                        }
                    },
                    {
                        "nodeType": "Scalar_String",
                        "value": "\n",
                        "attributes": {
                            "startLine": 4,
                            "endLine": 4,
                            "kind": 2
                        }
                    }
                ],
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                }
            }
        ],
        "attributes": {
            "startLine": 3,
            "endLine": 5
        }
    },
    {
        "nodeType": "Expr_FuncCall",
        "name": {
            "nodeType": "Name",
            "parts": [
                "printLine"
            ],
            "attributes": {
                "startLine": 7,
                "endLine": 7
            }
        },
        "args": [
            {
                "nodeType": "Arg",
                "value": {
                    "nodeType": "Scalar_String",
                    "value": "Hello World!!!",
                    "attributes": {
                        "startLine": 7,
                        "endLine": 7,
                        "kind": 1
                    }
                },
                "byRef": false,
                "unpack": false,
                "attributes": {
                    "startLine": 7,
                    "endLine": 7
                }
            }
        ],
        "attributes": {
            "startLine": 7,
            "endLine": 7
        }
    }
]
```

The JSON representation may be converted back into a node tree using the `JsonDecoder`:

```php
<?php

$nodeDecoder = new PhpParser\NodeDecoder();
$ast = $nodeDecoder->decode($json);
```

Note that not all ASTs can be represented using JSON. In particular:

 * JSON only supports UTF-8 strings.
 * JSON does not support non-finite floating-point numbers. This can occur if the original source
   code contains non-representable floating-pointing literals such as `1e1000`.

If the node tree is not representable in JSON, the initial `json_encode()` call will fail.