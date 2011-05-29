<?php

echo '<pre>';

echo 'Building parser. Output: "',
     `kmyacc -l -v -L c -m php.kmyacc -p Parser zend_language_parser.phpy`,
     '"', "\n";

$source = file_get_contents('y.tab.c');
$source = str_replace(
    '"$EOF"',
    '\'$EOF\'',
    $source
);

echo 'Moving parser to lib/Parser.php.', "\n";
file_put_contents(dirname(__DIR__) . '/lib/Parser.php', $source);
unlink(__DIR__ . '/y.tab.c');

echo 'Building debug parser. Output: "',
     `kmyacc -l -v -t -L c -m php.kmyacc -p ParserDebug zend_language_parser.phpy`,
     '"', "\n";

$source = file_get_contents('y.tab.c');
$source = str_replace(
    array(
        '"$EOF"',
        '"$start : start"'
    ),
    array(
        '\'$EOF\'',
        '\'$start : start\''
    ),
    $source
);

echo 'Moving debug parser to lib/ParserDebug.php.', "\n";
file_put_contents(dirname(__DIR__) . '/lib/ParserDebug.php', $source);
unlink(__DIR__ . '/y.tab.c');

echo 'Done.';

echo '</pre>';