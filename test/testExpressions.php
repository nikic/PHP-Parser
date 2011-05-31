<?php

$exprs = <<<'EXPRS'
a::$b
$a::$b
a::${b}
$a::${b}
a::$$b
$a::$$b
EXPRS;

function __autoload($class) {
    is_file($file = '../lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

$parser = new Parser;

include './testFormatting.html';

echo '<table>
    <tr>
        <td>Expression</td>
        <td>Result</td>
    </tr>';

foreach (explode("\n", $exprs) as $expr) {
    if ('' === $expr) {
        continue;
    }

    if ($parser->parse(
            new Lexer('<?php ' . $expr . ';'),
            function ($msg) use(&$errMsg) {
                $errMsg = $msg;
            }
        )
    ) {
        echo '<tr><td>' . $expr . '</td><td class="pass">PASS</td></tr>';
    } else {
        echo '<tr><td>' . $expr . '</td><td class="fail">FAIL</td></tr>';
    }
}

echo '</table>';