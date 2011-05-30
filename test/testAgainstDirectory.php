<?php

$DIR = '../../symfonySandbox';

function __autoload($class) {
    is_file($file = '../lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

$parser        = new Parser;
$prettyPrinter = new PrettyPrinter_Zend;
$nodeDumper    = new NodeDumper;

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

    .failCount {
        color: red;
    }
</style>
<table>
    <tr>
        <td>File</td>
        <td>Parse</td>
        <td>Time</td>
        <td>PrettyPrint</td>
        <td>Compare</td>
    </tr>';

$totalStartTime = microtime(true);
$parseFail = $parseCount = $ppFail = $ppCount = $compareFail = $compareCount = 0;

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

    ++$parseCount;
    if (false !== $stmts) {
        $code = '<?php' . "\n" . $prettyPrinter->pStmts($stmts);

        $ppStmts = $parser->yyparse(
            new Lexer($code),
            function($msg) use (&$errMsg) {
                $errMsg = $msg;
            }
        );

        ++$ppCount;
        if (false !== $ppStmts) {
            ++$compareCount;
            if ($nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts)) {
                echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="pass">PASS</td>
        <td class="pass">PASS</td>
    </tr>';            } else {
                echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="pass">PASS</td>
        <td class="fail">FAIL</td>
    </tr>';

                ++$compareFail;
            }
        } else {
            echo '
        <td class="pass">PASS</td>
        <td>' . $time . 's</td>
        <td class="fail">FAIL</td>
        <td></td>
    </tr>';

            ++$ppFail;
        }
    } else {
        echo '
        <td class="fail">FAIL</td>
        <td>' . $time . 's</td>
        <td></td>
        <td></td>
    </tr>
    <tr class="failReason"><td colspan="5">' . $errMsg . '</td></tr>';

        ++$parseFail;
    }

    flush();
}

echo '
    <tr>
        <td>Fail / Total:</td>
        <td><span class="failCount">' . $parseFail .   '</span> / ' . $parseCount .   '</td>
        <td></td>
        <td><span class="failCount">' . $ppFail .      '</span> / ' . $ppCount .      '</td>
        <td><span class="failCount">' . $compareFail . '</span> / ' . $compareCount . '</td>
    </tr>
</table>';

echo 'Total time: ', microtime(true) - $totalStartTime;