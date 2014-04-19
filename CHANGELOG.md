Version 1.0.0-dev
-----------------

* [PHP 5.6] Updated support for constant scalar expressions to comply with latest changes. This means that arrays
  and array dimension fetches are now supported as well.

Version 1.0.0-beta1 (27.03.2014)
--------------------------------

* [BC] PHP-Parser now requires PHP 5.3 or newer to run. It is however still possible to *parse* PHP 5.2 source code,
  while running on a newer version.

* [BC] The library has been moved to use namespaces with the `PhpParser` vendor prefix. However, the old names using
  underscores are still available as aliases, as such most code should continue running on the new version without
  further changes.

  However, code performing dispatch operations on `Node::getType()` may be affected by some of the name changes. For
  example a `+` node will now return type `Expr_BinaryOp_Plus` instead of `Expr_Plus`. In particular this may affect
  custom pretty printers.

  Due to conflicts with reserved keywords, some class names now end with an underscore, e.g. `PHPParser_Node_Stmt_Class`
  is now `PhpParser\Node\Stmt\Class_`. (But as usual, the old name is still available)

* [PHP 5.6] Added support for the power operator `**` (node `Expr\BinaryOp\Pow`) and the compound power assignment
  operator `**=` (node `Expr\AssignOp\Pow`).

* [PHP 5.6] Added support for variadic functions: `Param` nodes now have `variadic` as a boolean subnode.

* [PHP 5.6] Added support for argument unpacking: `Arg` nodes now have `unpack` as a boolean subnode.

* [PHP 5.6] Added support for aliasing of functions and constants. `Stmt\Use_` nodes now have an integral `type`
  subnode, which is one of `Stmt\Use_::TYPE_NORMAL` (`use`), `Stmt\Use_::TYPE_FUNCTION` (`use function`) or
  `Stmt\Use_::TYPE_CONSTANT` (`use const`).

  The `NameResolver` now also supports resolution of such aliases.

* [PHP 5.6] Added support for constant scalar expressions. This means that certain expressions are now allowed as the
  initializer for constants, properties, parameters, static variables, etc.

* [BC] Improved pretty printing of empty statements lists, which are now printed as `{\n}` instead of `{\n    \n}`.
  This changes the behavior of the protected `PrettyPrinterAbstract::pStmts()` method, so custom pretty printing code
  making use it of may need to be adjusted.

* Changed the order of some subnodes to be consistent with their order in the sour code. For example `Stmt\If->cond`
  will now appear before `Stmt\If->stmts` etc.

* Added `Scalar\MagicConstant->getName()`, which returns the name of the magic constant (e.g. `__CLASS__`).

**The following changes are also included in 0.9.5-dev**:

* [BC] Deprecated `PHPParser_Template` and `PHPParser_TemplateLoader`. This functionality does not belong in the main project
  and - as far as I know - nobody is using it.

* Add `NodeTraverser::removeVisitor()` method, which removes a visitor from the node traverser. This also modifies the
  corresponding `NodeTraverserInterface`.

* Fix alias resolution in `NameResolver`: Class names are now correctly handled as case-insensitive.

* The undefined variable error, which is used to the lexer to reset the error state, will no longer interfere with
  custom error handlers.

---

**This changelog only includes changes from the 1.0 series. For older changes see the [0.9 series changelog][1].**

 [1]: https://github.com/nikic/PHP-Parser/blob/0.9/CHANGELOG.md