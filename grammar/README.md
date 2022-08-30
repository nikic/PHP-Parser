What do all those files mean?
=============================

 * `php.y`:              PHP 5-8 grammar written in a pseudo language
 * `parser.template`:    A `kmyacc` parser prototype file for PHP
 * `rebuildParsers.php`: Preprocesses the grammar and builds the parser using `kmyacc`

.phpy pseudo language
=====================

The `.y` file is a normal grammar in `kmyacc` (`yacc`) style, with some transformations
applied to it:

 * Nodes are created using the syntax `Name[..., ...]`. This is transformed into
   `new Name(..., ..., attributes())`
 * Some function-like constructs are resolved (see `rebuildParsers.php` for a list)

Building the parser
===================

Run `php grammar/rebuildParsers.php` to rebuild the parsers. Additional options:

 * The `KMYACC` environment variable can be used to specify an alternative `kmyacc` binary.
   By default the `phpyacc` dev dependency will be used. To use the original `kmyacc`, you
   need to compile [moriyoshi's fork](https://github.com/moriyoshi/kmyacc-forked).
 * The `--debug` option enables emission of debug symbols and creates the `y.output` file.
 * The `--keep-tmp-grammar` option preserves the preprocessed grammar file.
