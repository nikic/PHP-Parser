Upgrading from PHP-Parser 4.x to 5.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 7.1 or newer to run. It is however still possible to *parse* code for older versions, while running on a newer version.

### PHP 5 parsing support

The dedicated parser for PHP 5 has been removed. The PHP 7 parser now accepts a `PhpVersion` argument, which can be used to improve compatibility with older PHP versions.

In particular, if an older `PhpVersion` is specified, then:

 * For versions before PHP 7.0, `$foo =& new Bar()` assignments are allowed without error.
 * For versions before PHP 7.0, invalid octal literals `089` are allowed without error.
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
 * The `PhpParser\ParserFactory::PREFER_PHP7` option is now equivalent to `ONLY_PHP7`.

### Changes to the parser factory

The `ParserFactory::create()` method is deprecated in favor of three new methods that provide more fine-grained control over the PHP version being targeted:

 * `createForNewestSupportedVersion()`: Use this if you don't know the PHP version of the code you're parsing. It's better to assume a too new version than a too old one.
 * `createForHostVersion()`: Use this if you're parsing code for the PHP version you're running on.
 * `createForVersion()`: Use this if you know the PHP version of the code you want to parse.

In all cases, the PHP version is a fairly weak hint that is only used on a best-effort basis. The parser will usually accept code for newer versions if it does not have any backwards-compatibility implications.

For example, if you specify version `"8.0"`, then `class ReadOnly {}` is treated as a valid class declaration, while using `public readonly int $prop` will lead to a parse error. However, `final public const X = Y;` will be accepted in both cases.

```php
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;

$factory = new ParserFactory;

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

### Renamed nodes

A number of AST nodes have been renamed or moved in the AST hierarchy:

 * `Node\Expr\ClosureUse` is now `Node\ClosureUse` and no longer extends `Node\Expr`. The `ClosureUse` node can only occur inside closure use lists, not as a general expression.

The old class names have been retained as aliases for backwards compatibility. However, the `Node::getType()` method will now always return the new name (e.g. `ClosureUse` instead of `Expr_ClosureUse`).

### Changes to the default pretty printer

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

The pretty printer now accepts a `phpVersion` option, which accepts a `PhpVersion` object and defaults to PHP 7.0. The pretty printer will make formatting choices to make the code valid for that version. It currently controls the following behavior:

* For PHP >= 7.0 (default), short array syntax `[]` will be used by default. This does not affect nodes that specify an explicit array syntax using the `kind` attribute.
* For PHP >= 7.3, a newline is no longer forced after heredoc/nowdoc strings, as the requirement for this has been removed with the introduction of flexible heredoc/nowdoc strings.

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

The `Lexer::getTokens()` method will now return an array of `Token`s, rather than an array of arrays and strings.
Additionally, the token array is now terminated by a sentinel token with ID 0.

### Other removed functionality

 * The deprecated `Builder\Param::setTypeHint()` method has been removed in favor of `Builder\Param::setType()`.
