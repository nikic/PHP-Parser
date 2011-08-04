What do all those files mean?
=============================

 * `zend_language_parser.y`:    Original PHP grammer this parser is based on
 * `zend_language_parser.phpy`: PHP grammer written in a pseudo language
 * `analyzer.php`:              Analyzes the `.phpy`-grammer and outputs some info about it
 * `rebuildParser.php`:         Preprocesses the `.phpy`-grammar and builds the parser using `kmyacc`
 * `php.kmyacc`:                A `kmyacc` parser prototype file for PHP

.phpy pseudo language
=========================

The `.phpy` file is a normal grammer in `kmyacc` (`yacc`) style, with some transformations
applied to it:

 * Nodes are created using the syntax `Name[subNode1: ..., subNode2: ...]`. This is transformed into
   `new PHPParser_Node_Name(array('subNode1' => ..., 'subNode2' => ...), $line, $docComment)`
 * `Name::abc` is transformed to `PHPParser_Node_Name::abc`
 * Some function-like constructs are resolved (see `rebuildParser.php` for a list)