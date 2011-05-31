<?php

function __autoload($class) {
    is_file($file = './lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

echo '<pre>';

$parser        = new Parser;
$prettyPrinter = new PrettyPrinter_Zend;

$code = $prettyPrinter->pStmts(
    $parser->parse(
        new Lexer(file_get_contents(
            '../symfonySandbox\src\vendor\symfony\src\Symfony\Components\Console\Input\InputDefinition.php'
        )),
        function ($msg) {
            echo $msg;
        }
    )
);

echo htmlspecialchars($code);