<?php

$DIR   = dirname(__FILE__) . '/../../Symfony';
$REGEX = '~skeleton(*COMMIT)(*FAIL)|\.php(\.cache)?$~';

require_once dirname(__FILE__) . '/../lib/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$parser        = new PHPParser_Parser;
$prettyPrinter = new PHPParser_PrettyPrinter_Zend;
$nodeDumper    = new PHPParser_NodeDumper;

$parseFail = $ppFail = $compareFail = 0;
$parseTime = $ppTime = $compareTime = 0;
$count = 0;

$totalStartTime = microtime(true);

if ('cli' !== php_sapi_name()) {
    echo '<pre>', "\n";
}

foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator($DIR),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if (!preg_match($REGEX, $file)) {
        continue;
    }

    set_time_limit(10);

    ++$count;

    try {
        $startTime = microtime(true);
        $stmts = $parser->parse(new PHPParser_Lexer(file_get_contents($file)));
        $parseTime += microtime(true) - $startTime;

        $startTime = microtime(true);
        $code = '<?php' . "\n" . $prettyPrinter->prettyPrint($stmts);
        $ppTime += microtime(true) - $startTime;

        try {
            $ppStmts = $parser->parse(new PHPParser_Lexer($code));

            $startTime = microtime(true);
            $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts);
            $compareTime += microtime(true) - $startTime;

            if (!$same) {
                echo $file, ":\n    Result of initial parse and parse after pretty print differ\n";

                ++$compareFail;
            }
        } catch (PHPParser_Error $e) {
            echo $file, ":\n    Parse of pretty print failed with message: {$e->getMessage()}\n";

            ++$ppFail;
        }
    } catch (PHPParser_Error $e) {
        echo $file, ":\n    Parse failed with message: {$e->getMessage()}\n";

        ++$parseFail;
    }
}

if (0 === $parseFail && 0 === $ppFail && 0 === $compareFail) {
    echo 'All tests passed.', "\n";
} else {
    echo "\n", '==========', "\n\n", 'There were: ', "\n";
    if (0 !== $parseFail) {
        echo '    ', $parseFail,   ' parse failures.',        "\n";
    }
    if (0 !== $ppFail) {
        echo '    ', $ppFail,      ' pretty print failures.', "\n";
    }
    if (0 !== $compareFail) {
        echo '    ', $compareFail, ' compare failures.',      "\n";
    }
}

echo "\n",
     'Tested files:         ', $count,        "\n",
     "\n",
     'Parsing took:         ', $parseTime,   "\n",
     'Pretty printing took: ', $ppTime,      "\n",
     'Comparing took:       ', $compareTime, "\n",
     "\n",
     'Total time:           ', microtime(true) - $totalStartTime, "\n",
     'Maximum memory usage: ', memory_get_peak_usage(true), "\n";

if ('cli' !== php_sapi_name()) {
    echo '</pre>';
}