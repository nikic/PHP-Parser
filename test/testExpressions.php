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

require_once '../lib/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$parser = new PHPParser_Parser;

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
        $parser->parse(new PHPParser_Lexer('<?php ' . $expr . ';'));

        echo '<tr><td>' . $expr . '</td><td class="pass">PASS</td></tr>';
    } catch (PHPParser_Error $e) {
        echo '<tr><td>' . $expr . '</td><td class="fail">FAIL</td></tr>';
        echo '<tr><td colspan="2">' .  $e->getMessage() . '</td></tr>';
    }
}

echo '</table>';