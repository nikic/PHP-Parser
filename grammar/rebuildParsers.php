<?php declare(strict_types=1);

require __DIR__ . '/phpyLang.php';

$parserToDefines = [
    'Php7' => ['PHP7' => true],
    'Php8' => ['PHP8' => true],
];

$grammarFile    = __DIR__ . '/php.y';
$skeletonFile   = __DIR__ . '/parser.template';
$tmpGrammarFile = __DIR__ . '/tmp_parser.phpy';
$tmpResultFile  = __DIR__ . '/tmp_parser.php';
$resultDir = __DIR__ . '/../lib/PhpParser/Parser';

$kmyacc = getenv('KMYACC');
if (!$kmyacc) {
    // Use phpyacc from dev dependencies by default.
    $kmyacc = __DIR__ . '/../vendor/bin/phpyacc';
}

$options = array_flip($argv);
$optionDebug = isset($options['--debug']);
$optionKeepTmpGrammar = isset($options['--keep-tmp-grammar']);

///////////////////
/// Main script ///
///////////////////

foreach ($parserToDefines as $name => $defines) {
    echo "Building temporary $name grammar file.\n";

    $grammarCode = file_get_contents($grammarFile);
    $grammarCode = replaceIfBlocks($grammarCode, $defines);
    $grammarCode = preprocessGrammar($grammarCode);

    file_put_contents($tmpGrammarFile, $grammarCode);

    $additionalArgs = $optionDebug ? '-t -v' : '';

    echo "Building $name parser.\n";
    $output = execCmd("$kmyacc $additionalArgs -m $skeletonFile -p $name $tmpGrammarFile");

    $resultCode = file_get_contents($tmpResultFile);
    $resultCode = removeTrailingWhitespace($resultCode);

    ensureDirExists($resultDir);
    file_put_contents("$resultDir/$name.php", $resultCode);
    unlink($tmpResultFile);

    if (!$optionKeepTmpGrammar) {
        unlink($tmpGrammarFile);
    }
}

////////////////////////////////
/// Utility helper functions ///
////////////////////////////////

function ensureDirExists($dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

function execCmd($cmd) {
    $output = trim(shell_exec("$cmd 2>&1") ?? '');
    if ($output !== "") {
        echo "> " . $cmd . "\n";
        echo $output;
    }
    return $output;
}

function replaceIfBlocks(string $code, array $defines): string {
    return preg_replace_callback('/\n#if\s+(\w+)\n(.*?)\n#endif/s', function ($matches) use ($defines) {
        $value = $defines[$matches[1]] ?? false;
        return $value ? $matches[2] : '';
    }, $code);
}
