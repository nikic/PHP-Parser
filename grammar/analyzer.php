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

const RULE_BLOCK = '(?<name>[a-z_]++):(?<rules>[^\'"/{};]*+(?:(?:(?&string)|(?&comment)|(?&code)|/|})[^\'"/{};]*+)*+);';

$tokensToExtract = array_flip(array(
    'T_VARIABLE', 'T_STRING', 'T_INLINE_HTML', 'T_ENCAPSED_AND_WHITESPACE',
    'T_LNUMBER', 'T_DNUMBER', 'T_CONSTANT_ENCAPSED_STRING', 'T_STRING_VARNAME', 'T_NUM_STRING'
));

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

function generateNodeFilesFromSignatures($signatures, $dir) {
    foreach ($signatures as $nodeName => $signature) {
        $code = <<<EOC
<?php

class Node_{$nodeName} extends NodeAbstract
{
}
EOC;
;
        if (!file_exists($dir . '/' . $nodeName . '.php')) {
            file_put_contents($dir . '/' . $nodeName . '.php', $code);
        }
    }
}

echo '<pre>';

////////////////////
////////////////////
////////////////////

list($defs, $ruleBlocks) = magicSplit('%%', file_get_contents(IN));

if ('' !== trim(preg_replace(regex(RULE_BLOCK), '', $ruleBlocks))) {
    die('Not all rule blocks were properly recognized!');
}

$nodeSignatures = array();

preg_match_all(regex(RULE_BLOCK), $ruleBlocks, $ruleBlocksMatches, PREG_SET_ORDER);
foreach ($ruleBlocksMatches as $match) {
    $ruleBlockName = $match['name'];
    $rules = magicSplit('\|', $match['rules']);

    foreach ($rules as &$rule) {
        $parts = magicSplit('\s+', $rule);
        $usedParts = array();

        foreach ($parts as $part) {
            if ('{' === $part[0]) {
                preg_match_all('~\$([0-9]+)~', $part, $backReferencesMatches, PREG_SET_ORDER);
                foreach ($backReferencesMatches as $match) {
                    $usedParts[$match[1]] = true;
                }

                preg_match_all('~(?<name>[A-Z][a-zA-Z]++)' . PARAMS . '~', $part, $nodeMatches, PREG_SET_ORDER);
                foreach ($nodeMatches as $match) {
                    $signature =& $nodeSignatures[$match['name']];
                    $params = magicSplit('(?:' . PARAMS . '|' . ARGS . ')(*SKIP)(*FAIL)|,', $match['params']);

                    if (!isset($signature)) {
                        $signature = array();
                        foreach ($params as $i => $param) {
                            list($name, ) = explode(': ', $param, 2);
                            $signature[$i] = $name;
                        }
                    } else {
                        foreach ($params as $i => $param) {
                            list($name, ) = explode(': ', $param, 2);
                            if ($signature[$i] != $name) {
                                die('Signature mismatch for "' . $match['name'] . '"');
                            }
                        }
                    }
                }
            }
        }

        $i = 1;
        foreach ($parts as &$part) {
            if ('/' === $part[0]) {
                continue;
            }

            if (isset($usedParts[$i])) {
                if ('\'' === $part[0] || '{' === $part[0]) {
                    $part = '<span style="background-color: red; color: white;">' . $part . '</span>';
                } elseif ('T' === $part[0] && !isset($tokensToExtract[$part])) {
                    $part = '<span style="background-color: green; color: white;">' . $part . '</span>';
                } else {
                    $part = '<strong><em>' . $part . '</em></strong>';
                }
            } elseif (ctype_lower($part[0])) {
                $part = '<span style="background-color: blue; color: white;">' . $part . '</span>';
            } elseif ('T' === $part[0] && isset($tokensToExtract[$part])) {
                $part = '<span style="background-color: yellow;">' . $part . '</span>';
            }

            ++$i;
        }

        $rule = implode(' ', $parts);
    }

    echo $ruleBlockName, ':', "\n", '      ', implode("\n" . '    | ', $rules), "\n", ';', "\n\n";
}

var_dump($nodeSignatures);

var_dump(array_keys($nodeSignatures));

$names = array();
foreach ($nodeSignatures as $params) {
    foreach ($params as $param) {
        $names[$param] = true;
    }
}

var_dump(array_keys($names));

generateNodeFilesFromSignatures($nodeSignatures, './lib/Node');