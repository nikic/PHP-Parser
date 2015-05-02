What do all those files mean?
=============================

 * `php5.y`:            PHP 5 grammer written in a pseudo language
 * `analyze.php`:       Analyzes the grammer and outputs some info about it
 * `rebuildParser.php`: Preprocesses the grammar and builds the parser using `kmyacc`
 * `kmyacc.php.parser`: A `kmyacc` parser prototype file for PHP

.phpy pseudo language
=====================

The `.y` file is a normal grammer in `kmyacc` (`yacc`) style, with some transformations
applied to it:

 * Nodes are created using the syntax `Name[..., ...]`. This is transformed into
   `new Name(..., ..., attributes())`
 * Some function-like constructs are resolved (see `rebuildParser.php` for a list)

Building the parser
===================

In order to rebuild the parser, you need [moriyoshi's fork of kmyacc](https://github.com/moriyoshi/kmyacc-forked).
After you compiled/installed it, run the `rebuildParser.php` script.

By default only the `Parser.php` is built. If you want to additionally emit debug symbols and create `y.output`, run the
script with `--debug`. If you want to retain the preprocessed grammar pass `--keep-tmp-grammar`.
