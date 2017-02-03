Upgrading from PHP-Parser 3.x to 4.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 5.6 or newer to run. It is however still possible to *parse* PHP 5.2-5.5
source code, while running on a newer version.

### Changes to the node structure

* Expression statements (`expr;`) are now represented using a `Stmt\Expression` node. Previously
  these statements were directly represented as their constituent expression.
* The `name` subnode of `Param` has been renamed to `var` and now contains a `Variable` rather than
  a plain string.
* The `name` subnode of `StaticVar` has been renamed to `var` and now contains a `Variable` rather
  than a plain string.
* The `var` subnode of `ClosureUse` now contains a `Variable` rather than a plain string.
* The `var` subnode of `Catch` now contains a `Variable` rather than a plain string.

### Removed functionality

* Removed `type` subnode on `Class`, `ClassMethod` and `Property` nodes. Use `flags` instead.
* The `ClassConst::isStatic()` method has been removed. Constants cannot have a static modifier.
* The `NodeTraverser` no longer accepts `false` as a return value from a `leaveNode()` method.
  `NodeTraverser::REMOVE_NODE` should be returned instead.