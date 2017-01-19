Upgrading from PHP-Parser 3.x to 4.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 5.6 or newer to run. It is however still possible to *parse* PHP 5.2-5.5
source code, while running on a newer version.

### Changes to the node structure

* Expression statements (`expr;`) are now represented using a `Stmt\Expression` node. Previously
  these statements were directly represented as their constituent expression.

### Removed functionality

* Removed `type` subnode on `Class`, `ClassMethod` and `Property` nodes. Use `flags` instead.