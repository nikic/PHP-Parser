Pretty printing
===============

Pretty printing is the process of converting a syntax tree back to PHP code. In its basic mode of
operation the pretty printer provided by this library will print the AST using a certain predefined
code style and will discard (nearly) all formatting of the original code. Because programmers tend
to be rather picky about their code formatting, this mode of operation is not very suitable for
refactoring code, but can be used for automatically generated code, which is usually only read for
debugging purposes.

Basic usage
-----------

```php
$stmts = $parser->parse($code);

// MODIFY $stmts here

$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$newCode = $prettyPrinter->prettyPrintFile($stmts);
```

The pretty printer has three basic printing methods: `prettyPrint()`, `prettyPrintFile()` and
`prettyPrintExpr()`. The one that is most commonly useful is `prettyPrintFile()`, which takes an
array of statements and produces a full PHP file, including opening `<?php`.

`prettyPrint()` also takes a statement array, but produces code which is valid inside an already
open `<?php` context. Lastly, `prettyPrintExpr()` takes an `Expr` node and prints only a single
expression.

Customizing the formatting
--------------------------

The pretty printer respects a number of `kind` attributes used by some nodes (e.g., whether an
integer should be printed as decimal, hexadecimal, etc). Additionally, it supports three options:

* `phpVersion` (defaults to 7.4) allows opting into formatting that is not supported by older PHP
  versions.
* `newline` (defaults to `"\n"`) can be set to `"\r\n"` in order to produce Windows newlines.
* `indent` (defaults to four spaces `"    "`) can be set to any number of spaces or a single tab.
* `shortArraySyntax` determines the used array syntax if the `kind` attribute is not set. This is
  a legacy option, and `phpVersion` should be used to control this behavior instead.

The behaviors controlled by `phpVersion` (defaults to PHP 7.4) are:

* For PHP >= 7.0, short array syntax `[]` will be used by default. This does not affect nodes that
  specify an explicit array syntax using the `kind` attribute.
* For PHP >= 7.0, parentheses around `yield` expressions will only be printed when necessary.
* For PHP >= 7.1, the short array syntax `[]` will be used for destructuring by default (instead of
  `list()`). This does not affect nodes that specify and explicit syntax using the `kind` attribute.
* For PHP >= 7.3, a newline is no longer forced after heredoc/nowdoc strings, as the requirement
  for this has been removed with the introduction of flexible heredoc/nowdoc strings.
* For PHP >= 7.3, heredoc/nowdoc strings are indented just like regular code. This was allowed with
  the introduction of flexible heredoc/nowdoc strings.

The default pretty printer does not provide functionality for fine-grained customization of code
formatting.

If you want to make minor changes to the formatting, the easiest way is to extend the pretty printer
and override the methods responsible for the node types you are interested in.

If you want to have more fine-grained formatting control, the recommended method is to combine the
default pretty printer with an existing library for code reformatting, such as
[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

Formatting-preserving pretty printing
-------------------------------------

For automated code refactoring, migration and similar, you will usually only want to modify a small
portion of the code and leave the remainder alone. The basic pretty printer is not suitable for
this, because it will also reformat parts of the code which have not been modified.

Since PHP-Parser 4.0, a formatting-preserving pretty-printing mode is available, which
attempts to preserve the formatting of code (those AST nodes that have not changed) and only reformat
code which has been modified or newly inserted.

Use of the formatting-preservation functionality requires some additional preparatory steps:

```php
use PhpParser\{NodeTraverser, NodeVisitor, ParserFactory, PrettyPrinter};

$parser = (new ParserFactory())->createForHostVersion();
$oldStmts = $parser->parse($code);
$oldTokens = $parser->getTokens();

// Run CloningVisitor before making changes to the AST.
$traverser = new NodeTraverser(new NodeVisitor\CloningVisitor());
$newStmts = $traverser->traverse($oldStmts);

// MODIFY $newStmts HERE

$printer = new PrettyPrinter\Standard();
$newCode = $printer->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
```

If you make use of the name resolution functionality, you will likely want to disable the
`replaceNodes` option. This will add resolved names as attributes, instead of directly modifying
the AST and causing spurious changes to the pretty printed code. For more information, see the
[name resolution documentation](Name_resolution.markdown).

The formatting-preservation works on a best-effort basis and may sometimes reformat more code than
necessary. If you encounter problems while using this functionality, please open an issue.
