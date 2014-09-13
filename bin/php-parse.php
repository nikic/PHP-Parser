<?php

require __DIR__ . '/../lib/bootstrap.php';

ini_set('xdebug.max_nesting_level', 2000);

/* The fancy var_dump function provided by XDebug will cut off the output way too
 * early to be of use. */
ini_set('xdebug.overload_var_dump', 0);

list($operations, $files) = parseArgs($argv);

/* Dump nodes by default */
if (empty($operations)) {
    $operations[] = 'dump';
}

if (empty($files)) {
    showHelp("Must specify at least one file.");
}

$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
$dumper = new PhpParser\NodeDumper;
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$serializer = new PhpParser\Serializer\XML;

$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);

foreach ($files as $file) {
    if (!file_exists($file)) {
        die("File $file does not exist.\n");
    }

    echo "====> File $file:\n";

    $code = file_get_contents($file);
    try {
        $stmts = $parser->parse($code);
    } catch (PhpParser\Error $e) {
        die("==> Parse Error: {$e->getMessage()}\n");
    }

    foreach ($operations as $operation) {
        if ('dump' === $operation) {
            echo "==> Node dump:\n";
            echo $dumper->dump($stmts), "\n";
        } elseif ('pretty-print' === $operation) {
            echo "==> Pretty print:\n";
            echo $prettyPrinter->prettyPrintFile($stmts), "\n";
        } elseif ('serialize-xml' === $operation) {
            echo "==> Serialized XML:\n";
            echo $serializer->serialize($stmts), "\n";
        } elseif ('var-dump' === $operation) {
            echo "==> var_dump():\n";
            var_dump($stmts);
        } elseif ('print-r' === $operation) {
            echo "==> print_r():\n";
            print_r($stmts);
        } elseif ('resolve-names' === $operation) {
            echo "==> Resolved names.\n";
            $stmts = $traverser->traverse($stmts);
        }
    }
}

function showHelp($error) {
    die($error . "\n\n" .
        <<<OUTPUT
Usage:

    php php-parse.php [operations] file1.php [file2.php ...]

Operations is a list of the following options (--dump by default):

    --dump           -d  Dump nodes using NodeDumper
    --pretty-print   -p  Pretty print file using PrettyPrinter\Standard
    --serialize-xml      Serialize nodes using Serializer\XML
    --var-dump           var_dump() nodes (for exact structure)
    --print-r            print_r() nodes (for exact structure)
    --resolve-names  -N  Resolve names using NodeVisitor\NameResolver

Example:

    php php-parse.php -d -p -N -d file.php

    Dumps nodes, pretty prints them, then resolves names and dumps them again.
OUTPUT
    );
}

function parseArgs($args) {
    $operations = array();
    $files = array();

    array_shift($args);
    $parseOptions = true;
    foreach ($args as $arg) {
        if (!$parseOptions) {
            $files[] = $arg;
            continue;
        }

        switch ($arg) {
            case '--dump':
            case '-d':
                $operations[] = 'dump';
                break;
            case '--pretty-print':
            case '-p':
                $operations[] = 'pretty-print';
                break;
            case '--serialize-xml':
                $operations[] = 'serialize-xml';
                break;
            case '--var-dump':
                $operations[] = 'var-dump';
                break;
            case '--print-r':
                $operations[] = 'print-r';
                break;
            case '--resolve-names':
            case '-N';
                $operations[] = 'resolve-names';
                break;
            case '--':
                $parseOptions = false;
                break;
            default:
                if ($arg[0] === '-') {
                    showHelp("Invalid operation $arg.");
                } else {
                    $files[] = $arg;
                }
        }
    }

    return array($operations, $files);
}