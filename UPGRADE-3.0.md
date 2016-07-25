Upgrading from PHP-Parser 2.x to 3.0
====================================

This version does not include any major API changes. Only specific details of the node
representation have changed in some cases.

### PHP version requirements

PHP-Parser now requires PHP 5.5 or newer to run. It is however still possible to *parse* PHP 5.2,
5.3 and 5.4 source code, while running on a newer version.

### Changes to the node structure

The following changes are likely to require code changes if the respective nodes are used:

 * The `List` subnode `vars` has been renamed to `items` and now contains `ArrayItem`s instead of
   plain variables.
 * The `Catch` subnode `type` has been renamed to `types` and is now an array of `Name`s.
 * The `TryCatch` subnode `finallyStmts` has been replaced with a `finally` subnode that holds an
   explicit `Finally` node.
 * The `type` subnode on `Class`, `ClassMethod` and `Property` has been renamed to `flags`. The
   `type` subnode has retained for backwards compatibility and is populated to the same value as
   `flags`. However, writes to `type` will not update `flags` and use of `type` is discouraged.

The following changes are unlikely to require code changes:

 * The `ClassConst` constructor changed to accept an additional `flags` subnode.
 * The `Trait` constructor now has the same form as the `Class` and `Interface` constructors: It
   takes an array of subnodes. Unlike classes/interfaces, traits can only have a `stmts` subnode.
 * The `Array` subnode `items` may now contain `null` elements (due to destructuring).
 * `void` and `iterable` types are now stored as strings if the PHP 7 parser is used. Previously
   these would have been represented as `Name` instances.

### Removed methods

The following methods have been removed:

 * `Comment::setLine()`, `Comment::setText()`: Create new `Comment` instances instead.
 * `Name::set()`, `Name::setFirst()`, `Name::setLast()`: Create new `Name` instances instead. For
   the latter two a combination of `Name::concat()` and `Name::slice()` can be used.

### Miscellaneous

 * All methods on `PrettyPrinter\Standard` are now protected. Previoulsy most of them were public.
   The pretty printer should only be invoked using the `prettyPrint()`, `prettyPrintFile()` and
   `prettyPrintExpr()` methods.
 * The node dumper now prints numeric values that act as enums/flags in a string representation.
   If node dumper results are used in tests, updates may be needed to account for this.