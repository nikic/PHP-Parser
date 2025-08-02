Upgrading from PHP-Parser 4.x to 5.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 7.4 or newer to run. It is however still possible to *parse* code for older versions, while running on a newer version.

### PHP 5 parsing support

The dedicated parser for PHP 5 has been removed. The PHP 7 parser now accepts a `PhpVersion` argument, which can be used to improve compatibility with older PHP versions.

In particular, if an older `PhpVersion` is specified, then:

 * For versions before PHP 7.0, `$foo =& new Bar()` assignments are allowed without error.
 * For versions before PHP 7.0, invalid octal literals `089` are allowed without error.
 * For versions before PHP 7.0, unicode escape sequences `\u{123}` in strings are not parsed.
 * Type hints are interpreted as a class `Name` or as a built-in `Identifier` depending on PHP
   version, for example `int` is treated as a class name on PHP 5.6 and as a built-in on PHP 7.0.

However, some aspects of PHP 5 parsing are no longer supported:

 * Some variables like `$$foo[0]` are valid in both PHP 5 and PHP 7, but have different interpretation. In that case, the PHP 7 AST will always be constructed (`($$foo)[0]` rather than `${$foo[0]}`).
 * Declarations of the form `global $$var[0]` are not supported in PHP 7 and will cause a parse error. In error recovery mode, it is possible to continue parsing after such declarations.
 * The PHP 7 parser will accept many constructs that are not valid in PHP 5. However, this was also true of the dedicated PHP 5 parser.

The following symbols are affected by this removal:

 * The `PhpParser\Parser\Php5` class has been removed.
 * The `PhpParser\Parser\Multiple` class has been removed. While not strictly related to PHP 5 support, this functionality is no longer useful without it.
 * The `PhpParser\ParserFactory::ONLY_PHP5` and `PREFER_PHP5` options have been removed.

### Changes to the parser factory

The `ParserFactory::create()` method has been removed in favor of three new methods that provide more fine-grained control over the PHP version being targeted:

 * `createForNewestSupportedVersion()`: Use this if you don't know the PHP version of the code you're parsing. It's better to assume a too new version than a too old one.
 * `createForHostVersion()`: Use this if you're parsing code for the PHP version you're running on.
 * `createForVersion()`: Use this if you know the PHP version of the code you want to parse.

The `createForNewestSupportedVersion()` and `createForHostVersion()` are available since PHP-Parser 4.18.0, to allow libraries to support PHP-Parser 4 and 5 at the same time more easily.

In all cases, the PHP version is a fairly weak hint that is only used on a best-effort basis. The parser will usually accept code for newer versions if it does not have any backwards-compatibility implications.

For example, if you specify version `"8.0"`, then `class ReadOnly {}` is treated as a valid class declaration, while using `public readonly int $prop` will lead to a parse error. However, `final public const X = Y;` will be accepted in both cases.

```php
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;

$factory = new ParserFactory();

# Before
$parser = $factory->create(ParserFactory::PREFER_PHP7);

# After (this is roughly equivalent to PREFER_PHP7 behavior)
$parser = $factory->createForNewestSupportedVersion();
# Or
$parser = $factory->createForHostVersion();

# Before
$parser = $factory->create(ParserFactory::ONLY_PHP5);
# After (supported on a best-effort basis)
$parser = $factory->createForVersion(PhpVersion::fromString("5.6"));
```

### Changes to the throw representation

Previously, `throw` statements like `throw $e;` were represented using the `Stmt\Throw_` class,
while uses inside other expressions (such as `$x ?? throw $e`) used the `Expr\Throw_` class.

Now, `throw $e;` is represented as a `Stmt\Expression` that contains an `Expr\Throw_`. The
`Stmt\Throw_` class has been removed.

```php
# Code
throw $e;

# Before
Stmt_Throw(
    expr: Expr_Variable(
        name: e
    )
)

# After
Stmt_Expression(
    expr: Expr_Throw(
        expr: Expr_Variable(
            name: e
        )
    )
)
```

### Changes to the array destructuring representation

Previously, the `list($x) = $y` destructuring syntax was represented using a `Node\Expr\List_`
node, while `[$x] = $y` used a `Node\Expr\Array_` node, the same used for the creation (rather than
destructuring) of arrays.

Now, destructuring is always represented using `Node\Expr\List_`. The `kind` attribute with value
`Node\Expr\List_::KIND_LIST` or `Node\Expr\List_::KIND_ARRAY` specifies which syntax was actually
used.

```php
# Code
[$x] = $y;

# Before
Expr_Assign(
   var: Expr_Array(
       items: array(
           0: Expr_ArrayItem(
               key: null
               value: Expr_Variable(
                   name: x
               )
               byRef: false
               unpack: false
           )
       )
   )
   expr: Expr_Variable(
       name: y
   )
)

# After
Expr_Assign(
   var: Expr_List(
       items: array(
           0: ArrayItem(
               key: null
               value: Expr_Variable(
                   name: x
               )
               byRef: false
               unpack: false
           )
       )
   )
   expr: Expr_Variable(
       name: y
   )
)
```

### Changes to the name representation

Previously, `Name` nodes had a `parts` subnode, which stores an array of name parts, split by
namespace separators. Now, `Name` nodes instead have a `name` subnode, which stores a plain string.

For example, the name `Foo\Bar` was previously represented by `Name(parts: ['Foo', 'Bar'])` and is
now represented by `Name(name: 'Foo\Bar')` instead.

It is possible to convert the name to the previous representation using `$name->getParts()`. The
`Name` constructor continues to accept both the string and the array representation.

The `Name::getParts()` method is available since PHP-Parser 4.16.0, to allow libraries to support
PHP-Parser 4 and 5 at the same time more easily.

### Changes to the block representation

Previously, code blocks `{ ... }` were always flattened into their parent statement list. For
example `while ($x) { $a; { $b; } $c; }` would produce the same node structure as
`if ($x) { $a; $b; $c; }`, namely a `Stmt\While_` node whose `stmts` subnode is an array of three
statements.

Now, the nested `{ $b; }` block is represented using an explicit `Stmt\Block` node. However, the
outer `{ $a; { $b; } $c; }` block is still represented using a simple array in the `stmts` subnode.

```php
# Code
while ($x) { $a; { $b; } $c; }

# Before
Stmt_While(
    cond: Expr_Variable(
        name: x
    )
    stmts: array(
        0: Stmt_Expression(
            expr: Expr_Variable(
                name: a
            )
        )
        1: Stmt_Expression(
            expr: Expr_Variable(
                name: b
            )
        )
        2: Stmt_Expression(
            expr: Expr_Variable(
                name: c
            )
        )
    )
)

# After
Stmt_While(
    cond: Expr_Variable(
        name: x
    )
    stmts: array(
        0: Stmt_Expression(
            expr: Expr_Variable(
                name: a
            )
        )
        1: Stmt_Block(
            stmts: array(
                0: Stmt_Expression(
                    expr: Expr_Variable(
                        name: b
                    )
                )
            )
        )
        2: Stmt_Expression(
            expr: Expr_Variable(
                name: c
            )
        )
    )
)
```

### Changes to comment assignment

Previously, comments were assigned to all nodes starting at the same position. Now they will be
assigned to the outermost node only.

```php
# Code
// Comment
$a + $b;

# Before
Stmt_Expression(
    expr: Expr_BinaryOp_Plus(
        left: Expr_Variable(
            name: a
            comments: array(
                0: // Comment
            )
        )
        right: Expr_Variable(
            name: b
        )
        comments: array(
            0: // Comment
        )
    )
    comments: array(
        0: // Comment
    )
)

# After
Stmt_Expression(
    expr: Expr_BinaryOp_Plus(
        left: Expr_Variable(
            name: a
        )
        right: Expr_Variable(
            name: b
        )
    )
    comments: array(
        0: // Comment
    )
)
```

### Renamed nodes

A number of AST nodes have been renamed or moved in the AST hierarchy:

 * `Node\Scalar\LNumber` is now `Node\Scalar\Int_`.
 * `Node\Scalar\DNumber` is now `Node\Scalar\Float_`.
 * `Node\Scalar\Encapsed` is now `Node\Scalar\InterpolatedString`.
 * `Node\Scalar\EncapsedStringPart` is now `Node\InterpolatedStringPart` and no longer extends
   `Node\Scalar` or `Node\Expr`.
 * `Node\Expr\ArrayItem` is now `Node\ArrayItem` and no longer extends `Node\Expr`.
 * `Node\Expr\ClosureUse` is now `Node\ClosureUse` and no longer extends `Node\Expr`.
 * `Node\Stmt\DeclareDeclare` is now `Node\DeclareItem` and no longer extends `Node\Stmt`.
 * `Node\Stmt\PropertyProperty` is now `Node\PropertyItem` and no longer extends `Node\Stmt`.
 * `Node\Stmt\StaticVar` is now `Node\StaticVar` and no longer extends `Node\Stmt`.
 * `Node\Stmt\UseUse` is now `Node\UseItem` and no longer extends `Node\Stmt`.

The old class names have been retained as aliases for backwards compatibility. However, the `Node::getType()` method will now always return the new name (e.g. `ClosureUse` instead of `Expr_ClosureUse`).

### Modifiers

Modifier flags (as used by the `$flags` subnode of `Class_`, `ClassMethod`, `Property`, etc.) are now available as class constants on a separate `PhpParser\Modifiers` class, instead of being part of `PhpParser\Node\Stmt\Class_`, to make it clearer that these are used by many different nodes. The old constants are deprecated, but are still available.

```php
PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC    -> PhpParser\Modifiers::PUBLIC
PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED -> PhpParser\Modifiers::PROTECTED
PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE   -> PhpParser\Modifiers::PRIVATE
PhpParser\Node\Stmt\Class_::MODIFIER_STATIC    -> PhpParser\Modifiers::STATIC
PhpParser\Node\Stmt\Class_::MODIFIER_ABSTRACT  -> PhpParser\Modifiers::ABSTRACT
PhpParser\Node\Stmt\Class_::MODIFIER_FINAL     -> PhpParser\Modifiers::FINAL
PhpParser\Node\Stmt\Class_::MODIFIER_READONLY  -> PhpParser\Modifiers::READONLY
PhpParser\Node\Stmt\Class_::VISIBILITY_MODIFIER_MASK -> PhpParser\Modifiers::VISIBILITY_MASK
```

### Changes to node constructors

Node constructor arguments accepting types no longer accept plain strings. Either an `Identifier` or `Name` (or `ComplexType`) should be passed instead. This affects the following constructor arguments:

* The `'returnType'` key of `$subNodes` argument of `Node\Expr\ArrowFunction`.
* The `'returnType'` key of `$subNodes` argument of `Node\Expr\Closure`.
* The `'returnType'` key of `$subNodes` argument of `Node\Stmt\ClassMethod`.
* The `'returnType'` key of `$subNodes` argument of `Node\Stmt\Function_`.
* The `$type` argument of `Node\NullableType`.
* The `$type` argument of `Node\Param`.
* The `$type` argument of `Node\Stmt\Property`.
* The `$type` argument of `Node\ClassConst`.

To follow the previous behavior, an `Identifier` should be passed, which indicates a built-in type.

### Changes to the pretty printer

A number of changes to the standard pretty printer have been made, to make it match contemporary coding style conventions (and in particular PSR-12). Options to restore the previous behavior are not provided, but it is possible to override the formatting methods (such as `pStmt_ClassMethod`) with your preferred formatting.

Return types are now formatted without a space before the `:`:

```php
# Before
function test() : Type
{
}

# After
function test(): Type
{
}
```

`abstract` and `final` are now printed before visibility modifiers:

```php
# Before
public abstract function test();

# After
abstract public function test();
```

A space is now printed between `use` and the following `(` for closures:

```php
# Before
function () use($var) {
};

# After
function () use ($var) {
};
```

Backslashes in single-quoted strings are now only printed if they are necessary:

```php
# Before
'Foo\\Bar';
'\\\\';

# After
'Foo\Bar';
'\\\\';
```

`else if` structures will now omit redundant parentheses:

```php
# Before
else {
    if ($x) {
        // ...
    }
}

# After
else if ($x) {
     // ...
}
```

The pretty printer now accepts a `phpVersion` option, which accepts a `PhpVersion` object and defaults to PHP 7.4. The pretty printer will make formatting choices to make the code valid for that version. It currently controls the following behavior:

* For PHP >= 7.0 (default), short array syntax `[]` will be used by default. This does not affect nodes that specify an explicit array syntax using the `kind` attribute.
* For PHP >= 7.0 (default), parentheses around `yield` expressions will only be printed when necessary. Previously, parentheses were always printed, even if `yield` was used as a statement.
* For PHP >= 7.1 (default), the short array syntax `[]` will be used for destructuring by default (instead of `list()`). This does not affect nodes that specify an explicit syntax using the `kind` attribute.
* For PHP >= 7.3 (default), a newline is no longer forced after heredoc/nowdoc strings, as the requirement for this has been removed with the introduction of flexible heredoc/nowdoc strings.
* For PHP >= 7.3 (default), heredoc/nowdoc strings are now indented just like regular code. This was allowed with the introduction of flexible heredoc/nowdoc strings.

### Changes to precedence handling in the pretty printer

The pretty printer now more accurately models operator precedence. Especially for unary operators, less unnecessary parentheses will be printed. Conversely, many bugs where semantically meaningful parentheses were omitted have been fixed.

To support these changes, precedence is now handled differently in the pretty printer. The internal `p()` method, which is used to recursively print nodes, now has the following signature:
```php
protected function p(
    Node $node, int $precedence = self::MAX_PRECEDENCE, int $lhsPrecedence = self::MAX_PRECEDENCE,
    bool $parentFormatPreserved = false
): string;
```

The `$precedence` is the precedence of the direct parent operator (if any), while `$lhsPrecedence` is that precedence of the nearest binary operator on whose left-hand-side the node occurs. For unary operators, only the `$lhsPrecedence` is relevant.

Recursive calls in pretty-printer methods should generally continue calling `p()` without additional parameters. However, pretty-printer methods for operators that participate in precedence resolution need to be adjusted. For example, typical implementations for operators look as follows now:

```php
protected function pExpr_BinaryOp_Plus(
    BinaryOp\Plus $node, int $precedence, int $lhsPrecedence
): string {
    return $this->pInfixOp(
        BinaryOp\Plus::class, $node->left, ' + ', $node->right, $precedence, $lhsPrecedence);
}

protected function pExpr_UnaryPlus(
    Expr\UnaryPlus $node, int $precedence, int $lhsPrecedence
): string {
    return $this->pPrefixOp(Expr\UnaryPlus::class, '+', $node->expr, $precedence, $lhsPrecedence);
}
```

The new `$precedence` and `$lhsPrecedence` arguments need to be passed down to the `pInfixOp()`, `pPrefixOp()` and `pPostfixOp()` methods.

### Changes to the node traverser

If there are multiple visitors, the node traverser will now call `leaveNode()` and `afterTraverse()` methods in the reverse order of the corresponding `enterNode()` and `beforeTraverse()` calls:

```php
# Before
$visitor1->enterNode($node);
$visitor2->enterNode($node);
$visitor1->leaveNode($node);
$visitor2->leaveNode($node);

# After
$visitor1->enterNode($node);
$visitor2->enterNode($node);
$visitor2->leaveNode($node);
$visitor1->leaveNode($node);
```

Additionally, the special `NodeVisitor` return values have been moved from `NodeTraverser` to `NodeVisitor`. The old names are deprecated, but still available.

```php
PhpParser\NodeTraverser::REMOVE_NODE -> PhpParser\NodeVisitor::REMOVE_NODE
PhpParser\NodeTraverser::DONT_TRAVERSE_CHILDREN -> PhpParser\NodeVisitor::DONT_TRAVERSE_CHILDREN
PhpParser\NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN -> PhpParser\NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN
PhpParser\NodeTraverser::STOP_TRAVERSAL -> PhpParser\NodeVisitor::STOP_TRAVERSAL
```

Visitors can now also be passed directly to the `NodeTraverser` constructor:

```php
# Before (and still supported)
$traverser = new NodeTraverser();
$traverser->addVisitor(new NameResolver());

# After
$traverser = new NodeTraverser(new NameResolver());
```

### Changes to token representation

Tokens are now internally represented using the `PhpParser\Token` class, which exposes the same base interface as
the `PhpToken` class introduced in PHP 8.0. On PHP 8.0 or newer, `PhpParser\Token` extends from `PhpToken`, otherwise
it extends from a polyfill implementation. The most important parts of the interface may be summarized as follows:

```php
class Token {
    public int $id;
    public string $text;
    public int $line;
    public int $pos;

    public function is(int|string|array $kind): bool;
}
```

The token array is now an array of `Token`s, rather than an array of arrays and strings.
Additionally, the token array is now terminated by a sentinel token with ID 0.

### Changes to the lexer

The lexer API is reduced to a single `Lexer::tokenize()` method, which returns an array of tokens. The `startLexing()` and `getNextToken()` methods have been removed.

Responsibility for determining start and end attributes for nodes has been moved from the lexer to the parser. The lexer no longer accepts an options array. The `usedAttributes` option has been removed without replacement, and the parser will now unconditionally add the `comments`, `startLine`, `endLine`, `startFilePos`, `endFilePos`, `startTokenPos` and `endTokenPos` attributes.

There should no longer be a need to directly interact with the `Lexer` for end users, as the `ParserFactory` will create an appropriate instance, and no additional configuration of the lexer is necessary. To use formatting-preserving pretty printing, the setup boilerplate changes as follows:

```php
# Before

$lexer = new Lexer\Emulative([
    'usedAttributes' => [
        'comments',
        'startLine', 'endLine',
        'startTokenPos', 'endTokenPos',
    ],
]);

$parser = new Parser\Php7($lexer);
$oldStmts = $parser->parse($code);
$oldTokens = $lexer->getTokens();

$traverser = new NodeTraverser();
$traverser->addVisitor(new NodeVisitor\CloningVisitor());
$newStmts = $traverser->traverse($oldStmts);

# After

$parser = (new ParserFactory())->createForNewestSupportedVersion();
$oldStmts = $parser->parse($code);
$oldTokens = $parser->getTokens();

$traverser = new NodeTraverser(new NodeVisitor\CloningVisitor());
$newStmts = $traverser->traverse($oldStmts);
```

### Miscellaneous changes

 * The deprecated `Builder\Param::setTypeHint()` method has been removed in favor of `Builder\Param::setType()`.
 * The deprecated `Error` constructor taking a start line has been removed. Pass `['startLine' => $startLine]` attributes instead.
 * The deprecated `Comment::getLine()`, `Comment::getTokenPos()` and `Comment::getFilePos()` methods have been removed. Use `Comment::getStartLine()`, `Comment::getStartTokenPos()` and `Comment::getStartFilePos()` instead.
 * `Comment::getReformattedText()` now normalizes CRLF newlines to LF newlines.
 * The `Node::getLine()` method has been deprecated. Use `Node::getStartLine()` instead.
