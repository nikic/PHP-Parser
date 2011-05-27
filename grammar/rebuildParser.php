<?php

echo '<pre>';

echo 'Building parser. Output: "',
     `kmyacc -l -v -t -m kmyacc.class.php.parser -L c zend_language_parser.phpy`,
     '"', "\n";

echo 'Reading parser.', "\n";
$source = file_get_contents('y.tab.c');

echo 'Replacing "YYParser" -> "Parser", "$EOF" -> \'$EOF\', "$start : start" -> \'$start : start\'.', "\n";
$source = str_replace(
    array(
        'YYParser',
        '"$EOF"',
        '"$start : start"'
    ),
    array(
        'Parser',
        '\'$EOF\'',
        '\'$start : start\''
    ),
    $source
);

echo 'Moving parser to lib/Parser.php.', "\n";
file_put_contents(dirname(__DIR__) . '/lib/Parser.php', $source);
unlink(__DIR__ . '/y.tab.c');

echo 'Done.';

echo '</pre>';