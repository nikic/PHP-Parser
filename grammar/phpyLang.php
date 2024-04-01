<?php declare(strict_types=1);

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

///////////////////////////////
/// Preprocessing functions ///
///////////////////////////////

function preprocessGrammar($code) {
    $code = resolveNodes($code);
    $code = resolveMacros($code);
    $code = resolveStackAccess($code);
    $code = str_replace('$this', '$self', $code);

    return $code;
}

function resolveNodes($code) {
    return preg_replace_callback(
        '~\b(?<name>[A-Z][a-zA-Z_\\\\]++)\s*' . PARAMS . '~',
        function ($matches) {
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

            return 'new ' . $matches['name'] . '(' . $paramCode . 'attributes())';
        },
        $code
    );
}

function resolveMacros($code) {
    return preg_replace_callback(
        '~\b(?<!::|->)(?!array\()(?<name>[a-z][A-Za-z]++)' . ARGS . '~',
        function ($matches) {
            // recurse
            $matches['args'] = resolveMacros($matches['args']);

            $name = $matches['name'];
            $args = magicSplit(
                '(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,',
                $matches['args']
            );

            if ('attributes' === $name) {
                assertArgs(0, $args, $name);
                return '$this->getAttributes($this->tokenStartStack[#1], $this->tokenEndStack[$stackPos])';
            }

            if ('stackAttributes' === $name) {
                assertArgs(1, $args, $name);
                return '$this->getAttributes($this->tokenStartStack[' . $args[0] . '], '
                       . ' $this->tokenEndStack[' . $args[0] . '])';
            }

            if ('init' === $name) {
                return '$$ = array(' . implode(', ', $args) . ')';
            }

            if ('push' === $name) {
                assertArgs(2, $args, $name);

                return $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0];
            }

            if ('pushNormalizing' === $name) {
                assertArgs(2, $args, $name);

                return 'if (' . $args[1] . ' !== null) { ' . $args[0] . '[] = ' . $args[1] . '; } $$ = ' . $args[0] . ';';
            }

            if ('toBlock' == $name) {
                assertArgs(1, $args, $name);

                return 'if (' . $args[0] . ' instanceof Stmt\Block) { $$ = ' . $args[0] . '->stmts; } '
                     . 'else if (' . $args[0] . ' === null) { $$ = []; } '
                     . 'else { $$ = [' . $args[0] . ']; }';
            }

            if ('parseVar' === $name) {
                assertArgs(1, $args, $name);

                return 'substr(' . $args[0] . ', 1)';
            }

            if ('parseEncapsed' === $name) {
                assertArgs(3, $args, $name);

                return 'foreach (' . $args[0] . ' as $s) { if ($s instanceof Node\InterpolatedStringPart) {'
                       . ' $s->value = Node\Scalar\String_::parseEscapeSequences($s->value, ' . $args[1] . ', ' . $args[2] . '); } }';
            }

            if ('makeNop' === $name) {
                assertArgs(1, $args, $name);

                return $args[0] . ' = $this->maybeCreateNop($this->tokenStartStack[#1], $this->tokenEndStack[$stackPos])';
            }

            if ('makeZeroLengthNop' == $name) {
                assertArgs(1, $args, $name);

                return $args[0] . ' = $this->maybeCreateZeroLengthNop($this->tokenPos);';
            }

            return $matches[0];
        },
        $code
    );
}

function assertArgs($num, $args, $name) {
    if ($num != count($args)) {
        die('Wrong argument count for ' . $name . '().');
    }
}

function resolveStackAccess($code) {
    $code = preg_replace('/\$\d+/', '$this->semStack[$0]', $code);
    $code = preg_replace('/#(\d+)/', '$$1', $code);
    return $code;
}

function removeTrailingWhitespace($code) {
    $lines = explode("\n", $code);
    $lines = array_map('rtrim', $lines);
    return implode("\n", $lines);
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

    if ($pieces === ['']) {
        return [];
    }

    return $pieces;
}
