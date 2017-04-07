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

There is currently no mechanism to convert JSON back into a node tree. Furthermore, not all ASTs
can be JSON encoded. In particular, JSON only supports UTF-8 strings.

Serialization to XML
--------------------

It is also possible to serialize the node tree to XML using `PhpParser\Serializer\XML->serialize()`
and to unserialize it using `PhpParser\Unserializer\XML->unserialize()`. This is useful for
interfacing with other languages and applications or for doing transformation using XSLT.

```php
<?php
$code = <<<'CODE'
<?php

function printLine($msg) {
    echo $msg, "\n";
}

printLine('Hello World!!!');
CODE;

$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
$serializer = new PhpParser\Serializer\XML;

try {
    $stmts = $parser->parse($code);

    echo $serializer->serialize($stmts);
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```

Produces:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:node="http://nikic.github.com/PHPParser/XML/node" xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode" xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <scalar:array>
  <node:Stmt_Function line="2">
   <subNode:byRef>
    <scalar:false/>
   </subNode:byRef>
   <subNode:params>
    <scalar:array>
     <node:Param line="2">
      <subNode:name>
       <scalar:string>msg</scalar:string>
      </subNode:name>
      <subNode:default>
       <scalar:null/>
      </subNode:default>
      <subNode:type>
       <scalar:null/>
      </subNode:type>
      <subNode:byRef>
       <scalar:false/>
      </subNode:byRef>
     </node:Param>
    </scalar:array>
   </subNode:params>
   <subNode:stmts>
    <scalar:array>
     <node:Stmt_Echo line="3">
      <subNode:exprs>
       <scalar:array>
        <node:Expr_Variable line="3">
         <subNode:name>
          <scalar:string>msg</scalar:string>
         </subNode:name>
        </node:Expr_Variable>
        <node:Scalar_String line="3">
         <subNode:value>
          <scalar:string>
</scalar:string>
         </subNode:value>
        </node:Scalar_String>
       </scalar:array>
      </subNode:exprs>
     </node:Stmt_Echo>
    </scalar:array>
   </subNode:stmts>
   <subNode:name>
    <scalar:string>printLine</scalar:string>
   </subNode:name>
  </node:Stmt_Function>
  <node:Expr_FuncCall line="6">
   <subNode:name>
    <node:Name line="6">
     <subNode:parts>
      <scalar:array>
       <scalar:string>printLine</scalar:string>
      </scalar:array>
     </subNode:parts>
    </node:Name>
   </subNode:name>
   <subNode:args>
    <scalar:array>
     <node:Arg line="6">
      <subNode:value>
       <node:Scalar_String line="6">
        <subNode:value>
         <scalar:string>Hello World!!!</scalar:string>
        </subNode:value>
       </node:Scalar_String>
      </subNode:value>
      <subNode:byRef>
       <scalar:false/>
      </subNode:byRef>
     </node:Arg>
    </scalar:array>
   </subNode:args>
  </node:Expr_FuncCall>
 </scalar:array>
</AST>
```
