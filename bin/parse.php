<?php
require __DIR__ . '/../lib/bootstrap.php';

ini_set('xdebug.max_nesting_level', 2000);

$parser = new PHPParser_Parser(new PHPParser_Lexer);

if ($argc < 2) {
    echo "$argv[0] file.php [file-2.php ...]";
}

$files = $argv;
array_shift($files);

foreach ($files as $file) {
    if (!file_exists($file)) {
        exit( "File $file does not exist.");
    }
}

foreach ($files as $file) {
    $code = file_get_contents($file);
    try {
        $stmts = $parser->parse($code);
        print_r($stmts);
    } catch (PHPParser_Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }
}
