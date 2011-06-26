What do all those files mean?
=============================

 * `zend_language_parser.y`:        Original PHP grammer this parser is based on
 * `zend_language_parser.pre.phpy`: PHP grammer written in a pseudo language, which is transformed
   into a proper `kmyacc` grammer using `preprocessor.php`
 * `zend_language_parser.phpy`:     PHP grammer ready for `kmyacc`
 * `analyzer.php`:                  Analyzes the `.pre.phpy`-grammer and outputs some info about it
 * `preprocessor.php`:              Transforms a `.pre.phpy` grammar into a `.phpy` grammar
 * `rebuildParser.php`:             Builds the actual parser by calling `kmyacc`
 * `php.kmyacc`:                    A `kmyacc` parser prototype file for PHP
 * `y.output`:                      `kmyacc`s debug output

.pre.phpy pseudo language
=========================

The `.pre.phpy` file is a normal grammer in `kmyacc` (`yacc`) style, with some transformations
applied to it:

 * Nodes are created using the syntax `Name[subNode1: ..., subNode2: ...]`. This is transformed into
   `new PHPParser_Node_Name(array('subNode1' => ..., 'subNode2' => ...), #this->line)` (`#` is used
   instead of `$` because `$` is a reservered character in `kmyacc` grammars. It is later transformed
   back to `$`)
 * `Name::abc` is transformed to `PHPParser_Node_Name::abc`
 * Some function-like constructs are resolved (see `preprocessor.php` for a list)