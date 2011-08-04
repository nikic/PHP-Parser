<?php

echo '<pre>';

echo 'Building parser. Output: "',
     trim(`kmyacc -l -L c -m php.kmyacc -p PHPParser_Parser zend_language_parser.phpy 2>&1`),
     '"', "\n";

$source = file_get_contents('y.tab.c');
$source = str_replace(
    array(
        '"$EOF"',
        '#',
    ),
    array(
        '\'$EOF\'',
        '$',
    ),
    $source
);

echo 'Moving parser to lib/PHPParser/Parser.php.', "\n";
file_put_contents(dirname(__DIR__) . '/lib/PHPParser/Parser.php', $source);
unlink(__DIR__ . '/y.tab.c');

echo 'Building debug parser. Output: "',
     trim(`kmyacc -l -t -L c -m php.kmyacc -p PHPParser_ParserDebug zend_language_parser.phpy 2>&1`),
     '"', "\n";

$source = file_get_contents('y.tab.c');
$source = str_replace(
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
    $source
);

echo 'Moving debug parser to lib/PHPParser/ParserDebug.php.', "\n";
file_put_contents(dirname(__DIR__) . '/lib/PHPParser/ParserDebug.php', $source);
unlink(__DIR__ . '/y.tab.c');

echo 'Done.';

echo '</pre>';