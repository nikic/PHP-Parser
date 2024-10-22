JSON representation
===================

Nodes (and comments) implement the `JsonSerializable` interface. As such, it is possible to JSON
encode the AST directly using `json_encode()`:

```php
<?php

use PhpParser\ParserFactory;

$code = <<<'CODE'
<?php

/** @param string $msg */
function printLine($msg) {
    echo $msg, "\n";
}
CODE;

$parser = (new ParserFactory())->createForHostVersion();

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
        "attributes": {
            "startLine": 4,
            "comments": [
                {
                    "nodeType": "Comment_Doc",
                    "text": "\/** @param string $msg *\/",
                    "line": 3,
                    "filePos": 7,
                    "tokenPos": 2,
                    "endLine": 3,
                    "endFilePos": 31,
                    "endTokenPos": 2
                }
            ],
            "endLine": 6
        },
        "byRef": false,
        "name": {
            "nodeType": "Identifier",
            "attributes": {
                "startLine": 4,
                "endLine": 4
            },
            "name": "printLine"
        },
        "params": [
            {
                "nodeType": "Param",
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                },
                "type": null,
                "byRef": false,
                "variadic": false,
                "var": {
                    "nodeType": "Expr_Variable",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4
                    },
                    "name": "msg"
                },
                "default": null,
                "flags": 0,
                "attrGroups": []
            }
        ],
        "returnType": null,
        "stmts": [
            {
                "nodeType": "Stmt_Echo",
                "attributes": {
                    "startLine": 5,
                    "endLine": 5
                },
                "exprs": [
                    {
                        "nodeType": "Expr_Variable",
                        "attributes": {
                            "startLine": 5,
                            "endLine": 5
                        },
                        "name": "msg"
                    },
                    {
                        "nodeType": "Scalar_String",
                        "attributes": {
                            "startLine": 5,
                            "endLine": 5,
                            "kind": 2,
                            "rawValue": "\"\\n\""
                        },
                        "value": "\n"
                    }
                ]
            }
        ],
        "attrGroups": [],
        "namespacedName": null
    }
]
```

The JSON representation may be converted back into an AST using the `JsonDecoder`:

```php
<?php

$jsonDecoder = new PhpParser\JsonDecoder();
$ast = $jsonDecoder->decode($json);
```

Note that not all ASTs can be represented using JSON. In particular:

 * JSON only supports UTF-8 strings.
 * JSON does not support non-finite floating-point numbers. This can occur if the original source
   code contains non-representable floating-pointing literals such as `1e1000`.

If the node tree is not representable in JSON, the initial `json_encode()` call will fail.

From the command line, a JSON dump can be obtained using `vendor/bin/php-parse -j file.php`.
