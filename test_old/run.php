<?php

if ('cli' !== php_sapi_name()) {
    die('This script is designed for running on the command line.');
}

if (3 !== $argc) {
    die('This script expects exactly two arguments:
  1. The test type (either "Symfony" or "PHP")
  2. The path to the test files');
}

$TEST_TYPE = $argv[1];
$DIR = $argv[2];

if ('Symfony' === $TEST_TYPE) {
    $FILTER_FUNC = function ($path) {
        return preg_match('~\.php(?:\.cache)?$~', $path) && false === strpos($path, 'skeleton');
    };
} elseif ('PHP' === $TEST_TYPE) {
    $FILTER_FUNC = function ($path) {
        return preg_match('~\.phpt$~', $path);
    };
} else {
    die('The test type must be either "Symfony" or "PHP".');
}

ini_set('short_open_tag', false);

require_once dirname(__FILE__) . '/../lib/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$parser        = new PHPParser_Parser;
$prettyPrinter = new PHPParser_PrettyPrinter_Zend;
$nodeDumper    = new PHPParser_NodeDumper;

$parseFail = $ppFail = $compareFail = 0;
$parseTime = $ppTime = $compareTime = 0;
$count = 0;

$totalStartTime = microtime(true);

foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator($DIR),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if (!$FILTER_FUNC($file)) {
        continue;
    }

    $code = file_get_contents($file);

    if ('PHP' === $TEST_TYPE) {
        if (preg_match('~(?:
# skeleton files
  ext.gmp.tests.001
| ext.skeleton.tests.001
# multibyte encoded files
| ext.mbstring.tests.zend_multibyte-01
| Zend.tests.multibyte.multibyte_encoding_001
| Zend.tests.multibyte.multibyte_encoding_004
| Zend.tests.multibyte.multibyte_encoding_005
# token_get_all bug (https://bugs.php.net/bug.php?id=60097)
| Zend.tests.bug47516
)\.phpt$~x', $file)) {
            continue;
        }

        if (!preg_match('~--FILE--\s*(.*?)--[A-Z]+--~s', $code, $matches)) {
            continue;
        }
        if (preg_match('~--EXPECT(?:F|REGEX)?--\s*(?:Parse|Fatal) error~', $code)) {
            continue;
        }

        $code = $matches[1];
    }

    set_time_limit(10);

    ++$count;

    try {
        $startTime = microtime(true);
        $stmts = $parser->parse(new PHPParser_Lexer($code));
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