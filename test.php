<?php

function __autoload($class) {
    is_file($file = './lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

echo '<pre>';

$parser = new Parser();

// Output Demo
$stmts = $parser->yyparse(new Lexer(
    '<?php
        x::$y[z];
        $x->y[z];
        $x->y[z][k]->l()->m[t];
        $x->y[z]();
        $x->$y[z]();
        $x->$$y[z]();'
    ),
    function ($msg) {
        echo $msg;
    }
);

if (false !== $stmts) {
    foreach ($stmts as $stmt) {
        echo htmlspecialchars($stmt), "\n";
    }
}

echo "\n\n";

$prettyPrinter = new PrettyPrinter_Zend;
$code = $prettyPrinter->pStmts(
    $parser->yyparse(
        new Lexer(file_get_contents(
            '../symfonySandbox\src\vendor\symfony\src\Symfony\Components\Console\Input\InputDefinition.php'
        )),
        function ($msg) {
            echo $msg;
        }
    )
);

echo htmlspecialchars($code);