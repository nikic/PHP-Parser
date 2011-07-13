<?php

$DIR = '../../Symfony';

require_once '../lib/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$parser        = new PHPParser_Parser;
$prettyPrinter = new PHPParser_PrettyPrinter_Zend;
$nodeDumper    = new PHPParser_NodeDumper;

include './testFormatting.html';

echo '<table>
    <tr>
        <td>File</td>
        <td>Parse</td>
        <td>PrettyPrint</td>
        <td>Compare</td>
    </tr>';

$parseFail = $parseCount = $ppFail = $ppCount = $compareFail = $compareCount = 0;

$parseTime = $ppTime = $compareTime = 0;

$totalStartTime = microtime(true);


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

    try {
        ++$parseCount;
        $startTime = microtime(true);
        $stmts = $parser->parse(new PHPParser_Lexer(file_get_contents($file)));
        $parseTime += microtime(true) - $startTime;

        ++$ppCount;
        $startTime = microtime(true);
        $code = '<?php' . "\n" . $prettyPrinter->prettyPrint($stmts);
        $ppTime += microtime(true) - $startTime;

        try {
            $ppStmts = $parser->parse(new PHPParser_Lexer($code));

            ++$compareCount;
            $startTime = microtime(true);
            $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts);
            $compareTime += microtime(true) - $startTime;

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
        } catch (PHPParser_Error $e) {
            echo '
        <td class="pass">PASS</td>
        <td class="fail">FAIL</td>
        <td></td>
    </tr>
    <tr class="failReason"><td colspan="4">' . $e->getMessage() . '</td></tr>';

            ++$ppFail;
        }
    } catch (PHPParser_Error $e) {
        echo '
        <td class="fail">FAIL</td>
        <td></td>
        <td></td>
    </tr>
    <tr class="failReason"><td colspan="4">' . $e->getMessage() . '</td></tr>';

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