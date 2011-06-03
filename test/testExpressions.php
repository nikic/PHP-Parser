<?php

$exprs = <<<'EXPRS'
a::$b
$a::$b
a::${b}
$a::${b}
a::$$b
$a::$$b

a::$b()
a::$b[c]()
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

    try {
        $parser->parse(new Lexer('<?php ' . $expr . ';'));

        echo '<tr><td>' . $expr . '</td><td class="pass">PASS</td></tr>';
    } catch (ParseErrorException $e) {
        echo '<tr><td>' . $expr . '</td><td class="fail">FAIL</td></tr>';
        echo '<tr><td colspan="2">' .  $e->getMessage() . '</td></tr>';
    }
}

echo '</table>';