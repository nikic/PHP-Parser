PHP Parser
==========

This is a PHP 5.2 to PHP 5.6 parser written in PHP. It's purpose is to simplify static code analysis and
manipulation.

[**Documentation for version 1.0.x**][doc_master] (stable; for running on PHP >= 5.3).

[Documentation for version 0.9.x][doc_0_9] (unsupported; for running on PHP 5.2).

In a Nutshell
-------------

The parser turns PHP source code into an abstract syntax tree. For example, if you pass the following code into the
parser:

```php
<?php
echo 'Hi', 'World';
hello\world('foo', 'bar' . 'baz');
```

You'll get a syntax tree looking roughly like this:

```
array(
    0: Stmt_Echo(
        exprs: array(
            0: Scalar_String(
                value: Hi
            )
            1: Scalar_String(
                value: World
            )
        )
    )
    1: Expr_FuncCall(
        name: Name(
            parts: array(
                0: hello
                1: world
            )
        )
        args: array(
            0: Arg(
                value: Scalar_String(
                    value: foo
                )
                byRef: false
            )
            1: Arg(
                value: Expr_Concat(
                    left: Scalar_String(
                        value: bar
                    )
                    right: Scalar_String(
                        value: baz
                    )
                )
                byRef: false
            )
        )
    )
)
```

You can then work with this syntax tree, for example to statically analyze the code (e.g. to find
programming errors or security issues).

Additionally, you can convert a syntax tree back to PHP code. This allows you to do code preprocessing
(like automatedly porting code to older PHP versions).

So, that's it, in a nutshell. You can find everything else in the [docs][doc_master].

 [doc_0_9]: https://github.com/nikic/PHP-Parser/tree/0.9/doc
 [doc_master]: https://github.com/nikic/PHP-Parser/tree/master/doc