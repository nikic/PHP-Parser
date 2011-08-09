<?php

const GRAMMAR_FILE = './zend_language_parser.phpy';

///////////////////////////////
/// Utility regex constants ///
///////////////////////////////

const LIB = '(?(DEFINE)
    (?<singleQuotedString>\'[^\\\\\']*+(?:\\\\.[^\\\\\']*+)*+\')
    (?<doubleQuotedString>"[^\\\\"]*+(?:\\\\.[^\\\\"]*+)*+")
    (?<string>(?&singleQuotedString)|(?&doubleQuotedString))
    (?<comment>/\*[^*]*+(?:\*(?!/)[^*]*+)*+\*/)
    (?<code>\{[^\'"/{}]*+(?:(?:(?&string)|(?&comment)|(?&code)|/)[^\'"/{}]*+)*+})
)';

const PARAMS = '\[(?<params>[^[\]]*+(?:\[(?&params)\][^[\]]*+)*+)\]';
const ARGS   = '\((?<args>[^()]*+(?:\((?&args)\)[^()]*+)*+)\)';

///////////////////
/// Main script ///
///////////////////

echo '<pre>';

echo 'Building temporary preproprocessed grammar file.', "\n";

$grammarCode = file_get_contents(GRAMMAR_FILE);

$grammarCode = preg_replace('~[A-Z][a-zA-Z_]++::~', 'PHPParser_Node_$0', $grammarCode);
$grammarCode = resolveNodes($grammarCode);
$grammarCode = resolveMacros($grammarCode);
$grammarCode = preg_replace('~\$([a-zA-Z_]++)~', '#$1', $grammarCode);

$tmpGrammarFile = tempnam('.', 'tmpGrammarFile');
file_put_contents($tmpGrammarFile, $grammarCode);

echo 'Building parser.       Output: "',
     trim(`kmyacc -l -L c -m php.kmyacc -p PHPParser_Parser $tmpGrammarFile 2>&1`),
     '"', "\n";

$code = file_get_contents('y.tab.c');
$code = str_replace(
    array(
        '"$EOF"',
        '#',
    ),
    array(
        '\'$EOF\'',
        '$',
    ),
    $code
);

file_put_contents(dirname(__DIR__) . '/lib/PHPParser/Parser.php', $code);
unlink(__DIR__ . '/y.tab.c');

echo 'Building debug parser. Output: "',
     trim(`kmyacc -l -t -L c -m php.kmyacc -p PHPParser_ParserDebug $tmpGrammarFile 2>&1`),
     '"', "\n";

$code = file_get_contents('y.tab.c');
$code = str_replace(
    array(
        '"$EOF"',
        '"$start : start"',
        '#',
    ),
    array(
        '\'$EOF\'',
        '\'$start : start\'',
        '$',
    ),
    $code
);

file_put_contents(dirname(__DIR__) . '/lib/PHPParser/ParserDebug.php', $code);
unlink(__DIR__ . '/y.tab.c');

unlink($tmpGrammarFile);

echo 'The following temporary preproprocessed grammar file was used:', "\n", $grammarCode;

echo '</pre>';

///////////////////////////////
/// Preprocessing functions ///
///////////////////////////////

function resolveNodes($code) {
    return preg_replace_callback(
        '~(?<name>[A-Z][a-zA-Z_]++)' . PARAMS . '~',
        function($matches) {
            // recurse
            $matches['params'] = resolveNodes($matches['params']);

            $params = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['params']
            );

            if (array() === $params) {
                return 'new PHPParser_Node_' . $matches['name'] . '($line, $docComment)';
            }

            $withArray = false;

            $paramCodes = array();
            foreach ($params as $param) {
                if (false !== strpos($param, ': ')) {
                    $withArray = true;

                    list($key, $value) = explode(': ', $param, 2);
                    $paramCodes[] = '\'' . $key . '\' => ' . $value;
                } else {
                    $paramCodes[] = $param;
                }
            }

            if (!$withArray) {
                return 'new PHPParser_Node_' . $matches['name'] . '(' . implode(', ', $paramCodes) . ', $line, $docComment)';
            } else {
                return 'new PHPParser_Node_' . $matches['name'] . '(array(' . implode(', ', $paramCodes) . '), $line, $docComment)';
            }
        },
        $code
    );
}

function resolveMacros($code) {
    return preg_replace_callback(
        '~(?<name>error|init|push|pushNormalizing|toArray|parse(?:Var|Encapsed|LNumber|DNumber))' . ARGS . '~',
        function($matches) {
            // recurse
            $matches['args'] = resolveMacros($matches['args']);

            $name = $matches['name'];
            $args = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['args']
            );

            if ('error' == $name) {
                assertArgs(1, $args, $name);

                return 'throw new PHPParser_Error(' . $args[0] . ')';
            }

            if ('init' == $name) {
                return '$$ = array(' . implode(', ', $args) . ')';
            }

            if ('push' == $name) {
                assertArgs(2, $args, $name);

                return $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0];
            }

            if ('pushNormalizing' == $name) {
                assertArgs(2, $args, $name);

                return 'if (is_array(' . $args[1] . ')) { $$ = array_merge(' . $args[0] . ', ' . $args[1] . '); } else { ' . $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0] . '; }';
            }

            if ('toArray' == $name) {
                assertArgs(1, $args, $name);

                return 'is_array(' . $args[0] . ') ? ' . $args[0] . ' : array(' . $args[0] . ')';
            }

            if ('parseVar' == $name) {
                assertArgs(1, $args, $name);

                return 'substr(' . $args[0] . ', 1)';
            }

            if ('parseEncapsed' == $name) {
                assertArgs(1, $args, $name);

                return 'stripcslashes(' . $args[0] . ')';
            }

            if ('parseLNumber' == $name) {
                assertArgs(1, $args, $name);

                return '(int) ' . $args[0];
            }

            if ('parseDNumber' == $name) {
                assertArgs(1, $args, $name);

                return '(double) ' . $args[0];
            }
        },
        $code
    );
}

function assertArgs($num, $args, $name) {
    if ($num != count($args)) {
        die('Wrong argument count for ' . $name . '().');
    }
}

//////////////////////////////
/// Regex helper functions ///
//////////////////////////////

function regex($regex) {
    return '~' . LIB . '(?:' . str_replace('~', '\~', $regex) . ')~';
}

function magicSplit($regex, $string) {
    $pieces = preg_split(regex('(?:(?&string)|(?&comment)|(?&code))(*SKIP)(*FAIL)|' . $regex), $string);

    foreach ($pieces as &$piece) {
        $piece = trim($piece);
    }

    return array_filter($pieces);
}