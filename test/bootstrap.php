<?php

namespace PhpParser;

require __DIR__ . '/../vendor/autoload.php';

function canonicalize($str) {
    // normalize EOL style
    $str = str_replace("\r\n", "\n", $str);

    // trim newlines at end
    $str = rtrim($str, "\n");

    // remove trailing whitespace on all lines
    $lines = explode("\n", $str);
    $lines = array_map(function($line) {
        return rtrim($line, " \t");
    }, $lines);
    return implode("\n", $lines);
}
