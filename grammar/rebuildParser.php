<?php

const GRAMMAR_FILE = './zend_language_parser.phpy';
const TMP_FILE     = './tmp_parser.phpy';
const RESULT_FILE  = './tmp_parser.php';

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

$grammarCode = resolveConstants($grammarCode);
$grammarCode = resolveNodes($grammarCode);
$grammarCode = resolveMacros($grammarCode);
$grammarCode = resolveArrays($grammarCode);

file_put_contents(TMP_FILE, $grammarCode);

echo 'Building parser. Output: "',
     trim(shell_exec('kmyacc -l -m kmyacc.php.parser -p PHPParser_Parser ' . TMP_FILE . ' 2>&1')),
     '"', "\n";

rename(RESULT_FILE, '../lib/PHPParser/Parser.php');

if (isset($_GET['debug'])) {
    echo 'Building debug parser. Output: "',
    trim(shell_exec('kmyacc -t -v -l -m kmyacc.php.parser -p PHPParser_Parser ' . TMP_FILE . ' 2>&1')),
    '"', "\n";

    if (!is_dir('../lib/PHPParser/Parser')) {
        mkdir('../lib/PHPParser/Parser');
    }
    rename(RESULT_FILE, '../lib/PHPParser/Parser/Debug.php');
}


unlink(TMP_FILE);

echo 'The following temporary preproprocessed grammar file was used:', "\n", $grammarCode;

echo '</pre>';

///////////////////////////////
/// Preprocessing functions ///
///////////////////////////////

function resolveConstants($code) {
    return preg_replace('~[A-Z][a-zA-Z_]++::~', 'PHPParser_Node_$0', $code);
}

function resolveNodes($code) {
    return preg_replace_callback(
        '~(?<name>[A-Z][a-zA-Z_]++)\s*' . PARAMS . '~',
        function($matches) {
            // recurse
            $matches['params'] = resolveNodes($matches['params']);

            $params = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['params']
            );

            $paramCode = '';
            foreach ($params as $param) {
                $paramCode .= $param . ', ';
            }

            return 'new PHPParser_Node_' . $matches['name'] . '(' . $paramCode . '$line, $docComment)';
        },
        $code
    );
}

function resolveMacros($code) {
    return preg_replace_callback(
        '~\b(?<!::|->)(?!array\()(?<name>[a-z][A-Za-z]++)' . ARGS . '~',
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
                assertArgs(2, $args, $name);

                return 'foreach (' . $args[0] . ' as &$s) { if (is_string($s)) { $s = PHPParser_Node_Scalar_String::parseEscapeSequences($s, ' . $args[1] . '); } }';
            }

            if ('parseEncapsedDoc' == $name) {
                assertArgs(1, $args, $name);

                return 'foreach (' . $args[0] . ' as &$s) { if (is_string($s)) { $s = PHPParser_Node_Scalar_String::parseEscapeSequences($s, null); } } $s = preg_replace(\'~(\r\n|\n|\r)$~\', \'\', $s); if (\'\' === $s) array_pop(' . $args[0] . ');';
            }

            throw new Exception(sprintf('Unknown macro "%s"', $name));
        },
        $code
    );
}

function assertArgs($num, $args, $name) {
    if ($num != count($args)) {
        die('Wrong argument count for ' . $name . '().');
    }
}

function resolveArrays($code) {
    return preg_replace_callback(
        '~' . PARAMS . '~',
        function ($matches) {
            $elements = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['params']
            );

            // don't convert [] to array, it might have different meaning
            if (empty($elements)) {
                return $matches[0];
            }

            $elementCodes = array();
            foreach ($elements as $element) {
                // convert only arrays where all elements have keys
                if (false === strpos($element, ':')) {
                    return $matches[0];
                }

                list($key, $value) = explode(':', $element, 2);
                $elementCodes[] = "'" . $key . "' =>" . $value;
            }

            return 'array(' . implode(', ', $elementCodes) . ')';
        },
        $code
    );
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