Upgrading from PHP-Parser 4.x to 5.0
====================================

### PHP version requirements

PHP-Parser now requires PHP 7.1 or newer to run. It is however still possible to *parse* code for older versions, while running on a newer version.

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
