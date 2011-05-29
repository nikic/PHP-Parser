<?php

$DIR = '..';

function __autoload($class) {
    is_file($file = '../lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

$parser = new Parser();
$parser->yydebug = false;

$prettyPrinter = new PrettyPrinter_Zend;

echo '<!DOCTYPE html>
<style>
    body {
        font-family: "Trebuchet MS", sans-serif;
    }

    .pass {
        color: white;
        background-color: green;
    }

    .fail {
        color: white;
        background-color: red;
    }

    .failReason {
        background-color: rgba(255, 0, 0, 0.3);
    }
</style>
<table>
    <tr>
        <td>File</td>
        <td>Parse</td>
        <td>Time</td>
        <td>PrettyPrint</td>
        <td>Same</td>
    </tr>';

$GST = microtime(true);
foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator($DIR),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if ('.php' !== substr($file, -4)) {
        continue;
    }

    echo '
    <tr>
        <td>' . $file . '</td>';

    set_time_limit(5);

    $errMsg = '';
    $startTime = microtime(true);

    $stmts = $parser->yyparse(
        new Lexer(file_get_contents($file)),
        function($msg) use (&$errMsg) {
            $errMsg = $msg;
        }
    );

    $time = microtime(true) - $startTime;

    if (false !== $stmts) {
        $code = '<?php' . "\n" . $prettyPrinter->pStmts($stmts);

        $ppStmts = $parser->yyparse(
            new Lexer($code),
            function($msg) use (&$errMsg) {
                $errMsg = $msg;
            }
        );

        if (false !== $ppStmts) {
            if ($stmts == $ppStmts) {
                echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="pass">PASS</td>
        <td class="pass">PASS</td>
    </tr>';
            } else {
                echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="pass">PASS</td>
        <td class="fail">FAIL</td>
    </tr>';
            }
        } else {
            echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="fail">FAIL</td>
        <td></td>
    </tr>';
        }
    } else {
        echo '
        <td class="fail">FAIL</td>
        <td>' . $time . 's</td>
        <td></td>
        <td></td>
    </tr>
    <tr class="failReason"><td colspan="5">' . $errMsg . '</td></tr>';
    }

    flush();
}

echo '
</table>';

echo 'Total time: ', microtime(true) - $GST;