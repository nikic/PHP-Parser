<?php

const IN  = './zend_language_parser.pre.phpy';
const OUT = './zend_language_parser.phpy';

const LIB = '(?(DEFINE)
    (?<singleQuotedString>\'[^\\\\\']*+(?:\\\\.[^\\\\\']*+)*+\')
    (?<doubleQuotedString>"[^\\\\"]*+(?:\\\\.[^\\\\"]*+)*+")
    (?<string>(?&singleQuotedString)|(?&doubleQuotedString))
    (?<comment>/\*[^*]*+(?:\*(?!/)[^*]*+)*+\*/)
    (?<code>\{[^\'"/{}]*+(?:(?:(?&string)|(?&comment)|(?&code)|/)[^\'"/{}]*+)*+})
)';

const PARAMS = '\[(?<params>[^[\]]*+(?:\[(?&params)\][^[\]]*+)*+)\]';
const ARGS   = '\((?<args>[^()]*+(?:\((?&args)\)[^()]*+)*+)\)';

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

echo '<pre>';

////////////////////
////////////////////
////////////////////

$code = file_get_contents(IN);

$code = preg_replace('~[A-Z][a-zA-Z]++::~', 'Node_$0', $code);
$code = resolveNodes($code);
$code = resolveMacros($code);

file_put_contents(OUT, $code);

echo $code;

function resolveNodes($code) {
    return preg_replace_callback(
        '~(?<name>[A-Z][a-zA-Z]++)' . PARAMS . '~',
        function($matches) {
            // recurse
            $matches['params'] = resolveNodes($matches['params']);

            $params = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['params']
            );

            $paramCodes = array();
            foreach ($params as $param) {
                list($key, $value) = explode(': ', $param, 2);

                $paramCodes[] = '\'' . $key . '\' => ' . $value;
            }

            return 'new Node_' . $matches['name'] . '(array(' . implode(', ', $paramCodes) . '))';
        },
        $code
    );
}

function resolveMacros($code) {
    return preg_replace_callback(
        '~(?<name>init|push|pushNormalizing|toArray|parse(?:Var|Encapsed|LNumber|DNumber|String))' . ARGS . '~',
        function($matches) {
            // recurse
            $matches['args'] = resolveMacros($matches['args']);

            $name = $matches['name'];
            $args = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['args']
            );

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

            if ('parseString' == $name) {
                assertArgs(1, $args, $name);

                return 'str_replace(array(\'\\\\\\\'\', \'\\\\\\\\\'), array(\'\\\'\', \'\\\\\'), substr(' . $args[0] . ', 1, -1))';
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