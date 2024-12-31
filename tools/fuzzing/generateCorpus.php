<?php declare(strict_types=1);

$testDir = __DIR__ . '/../../test';
require $testDir . '/bootstrap.php';
require $testDir . '/PhpParser/CodeTestParser.php';
require $testDir . '/PhpParser/CodeParsingTest.php';

$inputDirs = [$testDir . '/code/parser', $testDir . '/code/prettyPrinter'];

if ($argc < 2) {
    echo "Usage: php generateCorpus.php dir/\n";
    exit(1);
}

$corpusDir = $argv[1];
if (!is_dir($corpusDir)) {
    mkdir($corpusDir, 0777, true);
}

$testParser = new PhpParser\CodeTestParser();
$codeParsingTest = new PhpParser\CodeParsingTest();
foreach ($inputDirs as $inputDir) {
    foreach (PhpParser\filesInDir($inputDir, 'test') as $fileName => $code) {
        list($_name, $tests) = $testParser->parseTest($code, 2);
        foreach ($tests as list($_modeLine, list($input, $_expected))) {
            $path = $corpusDir . '/' . md5($input) . '.txt';
            file_put_contents($path, $input);
        }
    }
}
