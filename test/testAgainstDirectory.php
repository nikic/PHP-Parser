<?php

$DIR = '../../Symfony';

function __autoload($class) {
    is_file($file = '../lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

$parser        = new Parser;
$prettyPrinter = new PrettyPrinter_Zend;
$nodeDumper    = new NodeDumper;

include './testFormatting.html';

echo '<table>
    <tr>
        <td>File</td>
        <td>Parse</td>
        <td>PrettyPrint</td>
        <td>Compare</td>
    </tr>';

$parseFail = $parseCount = $ppFail = $ppCount = $compareFail = $compareCount = 0;

$totalStartTime = microtime(true);
$parseTime = $ppTime = $compareTime = 0;

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

    set_time_limit(10);

    $errMsg = '';

    ++$parseCount;
    $parseTime -= microtime(true);
    $stmts = $parser->parse(
        new Lexer(file_get_contents($file)),
        function ($msg) use (&$errMsg) {
            $errMsg = $msg;
        }
    );
    $parseTime += microtime(true);

    if (false !== $stmts) {
        ++$ppCount;
        $ppTime -= microtime(true);
        $code = '<?php' . "\n" . $prettyPrinter->pStmts($stmts);
        $ppTime += microtime(true);

        $ppStmts = $parser->parse(
            new Lexer($code),
            function ($msg) use (&$errMsg) {
                $errMsg = $msg;
            }
        );

        if (false !== $ppStmts) {
            ++$compareCount;
            $compareTime -= microtime(true);
            $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts);
            $compareTime += microtime(true);

            if ($same) {
                echo '
        <td class="pass">PASS</td>
        <td class="pass">PASS</td>
        <td class="pass">PASS</td>
    </tr>';
            } else {
                echo '
        <td class="pass">PASS</td>
        <td class="pass">PASS</td>
        <td class="fail">FAIL</td>
    </tr>';

                ++$compareFail;
            }
        } else {
            echo '
        <td class="pass">PASS</td>
        <td class="fail">FAIL</td>
        <td></td>
    </tr>';

            ++$ppFail;
        }
    } else {
        echo '
        <td class="fail">FAIL</td>
        <td></td>
        <td></td>
    </tr>
    <tr class="failReason"><td colspan="4">' . $errMsg . '</td></tr>';

        ++$parseFail;
    }

    flush();
}

echo '
    <tr>
        <td>Fail / Total:</td>
        <td><span class="failCount">' . $parseFail .   '</span> / ' . $parseCount .   '</td>
        <td><span class="failCount">' . $ppFail .      '</span> / ' . $ppCount .      '</td>
        <td><span class="failCount">' . $compareFail . '</span> / ' . $compareCount . '</td>
    </tr>
    <tr>
        <td>Time:</td>
        <td>' . $parseTime . '</td>
        <td>' . $ppTime . '</td>
        <td>' . $compareTime . '</td>
    </tr>
</table>';

echo 'Total time: ', microtime(true) - $totalStartTime, '<br />',
     'Maximum memory usage: ', memory_get_peak_usage(true);