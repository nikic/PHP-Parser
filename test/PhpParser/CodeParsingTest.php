<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class CodeParsingTest extends CodeTestAbstract {
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $expected, $modeLine): void {
        $modes = $this->parseModeLine($modeLine);
        $parser = $this->createParser($modes['version'] ?? null);
        list($stmts, $output) = $this->getParseOutput($parser, $code, $modes);

        $this->assertSame($expected, $output, $name);
        $this->checkAttributes($stmts);
    }

    public function createParser(?string $version): Parser {
        $factory = new ParserFactory();
        $version = $version === null
            ? PhpVersion::getNewestSupported() : PhpVersion::fromString($version);
        return $factory->createForVersion($version);
    }

    // Must be public for updateTests.php
    public function getParseOutput(Parser $parser, $code, array $modes) {
        $dumpPositions = isset($modes['positions']);
        $dumpOtherAttributes = isset($modes['attributes']);

        $errors = new ErrorHandler\Collecting();
        $stmts = $parser->parse($code, $errors);

        $output = '';
        foreach ($errors->getErrors() as $error) {
            $output .= $this->formatErrorMessage($error, $code) . "\n";
        }

        if (null !== $stmts) {
            $dumper = new NodeDumper([
                'dumpComments' => true,
                'dumpPositions' => $dumpPositions,
                'dumpOtherAttributes' => $dumpOtherAttributes,
            ]);
            $output .= $dumper->dump($stmts, $code);
        }

        return [$stmts, canonicalize($output)];
    }

    public static function provideTestParse() {
        return self::getTests(__DIR__ . '/../code/parser', 'test');
    }

    private function formatErrorMessage(Error $e, $code) {
        if ($e->hasColumnInfo()) {
            return $e->getMessageWithColumnInfo($code);
        }

        return $e->getMessage();
    }

    private function checkAttributes($stmts): void {
        if ($stmts === null) {
            return;
        }

        $traverser = new NodeTraverser(new class () extends NodeVisitorAbstract {
            public function enterNode(Node $node): void {
                $startLine = $node->getStartLine();
                $endLine = $node->getEndLine();
                $startFilePos = $node->getStartFilePos();
                $endFilePos = $node->getEndFilePos();
                $startTokenPos = $node->getStartTokenPos();
                $endTokenPos = $node->getEndTokenPos();
                if ($startLine < 0 || $endLine < 0 ||
                    $startFilePos < 0 || $endFilePos < 0 ||
                    $startTokenPos < 0 || $endTokenPos < 0
                ) {
                    throw new \Exception('Missing location information on ' . $node->getType());
                }

                if ($endLine < $startLine ||
                    $endFilePos < $startFilePos ||
                    $endTokenPos < $startTokenPos
                ) {
                    // Nop and Error can have inverted order, if they are empty.
                    // This can also happen for a Param containing an Error.
                    if (!$node instanceof Stmt\Nop && !$node instanceof Expr\Error &&
                        !$node instanceof Node\Param
                    ) {
                        throw new \Exception('End < start on ' . $node->getType());
                    }
                }
            }
        });
        $traverser->traverse($stmts);
    }
}
