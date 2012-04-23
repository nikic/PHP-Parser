Version 0.9.2-dev
-----------------

Nothing yet.

Version 0.9.1 (24.04.2012)
--------------------------

* Add ability to add attributes to nodes:

  It is now possible to add attributes to a node using `$node->setAttribute('name', 'value')` and to retrieve them using
  `$node->getAttribute('name' [, 'default'])`. Additionally the existance of an attribute can be checked with
  `$node->hasAttribute('name')` and all attributes can be returned using `$node->getAttributes()`.

* Add code generation features: Builders and templates.

  For more infos, see the [code generation documentation][1].

* [BC] Don't traverse nodes merged by another visitor:

  If a NodeVisitor returns an array of nodes to merge, these will no longer be traversed by all other visitors. This
  behavior only caused problems.

* Fix line numbers for some list structures
* Fix XML unserialization of empty nodes
* Fix parsing of integers that overflow into floats
* Fix emulation of NOWDOC and binary floats

Version 0.9.0 (05.01.2012)
--------------------------

First version.

 [1]: https://github.com/nikic/PHP-Parser/blob/master/doc/3_Code_generation.markdown