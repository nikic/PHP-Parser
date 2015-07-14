Upgrading from PHP-Parser 1.x to 2.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 5.4 or newer to run. It is however still possible to *parse* PHP 5.2 and
PHP 5.3 source code, while running on a newer version.

###

### Miscellaneous

* The `NodeTraverser` no longer clones nodes by default. If you want to restore the old behavior,
  pass `true` to the constructor.
