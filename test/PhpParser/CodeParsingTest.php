<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class CodeParsingTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $expected, $modeLine) {
        if (null !== $modeLine) {
            $modes = $this->parseModeLine($modeLine);
        } else {
            $modes = [];
        }

        $parser = $this->createParser($modes['version'] ?? null);
        list($stmts, $output) = $this->getParseOutput($parser, $code, $modes);

        $this->assertSame($expected, $output, $name);
        $this->checkAttributes($stmts);
    }

    private function parseModeLine(string $modeLine): array {
        $modes = [];
        foreach (explode(',', $modeLine) as $mode) {
            $kv = explode('=', $mode, 2);
            if (isset($kv[1])) {
                $modes[$kv[0]] = $kv[1];
            } else {
                $modes[$kv[0]] = true;
            }
        }
        return $modes;
    }

    public function createParser(?string $version): Parser {
        $factory = new ParserFactory();
        return $factory->createForVersion(
            $version ?? $factory->getNewestSupportedVersion(),
            ['usedAttributes' => [
                'startLine', 'endLine',
                'startFilePos', 'endFilePos',
                'startTokenPos', 'endTokenPos',
                'comments'
            ]]);
    }

    // Must be public for updateTests.php
    public function getParseOutput(Parser $parser, $code, array $modes) {
        $dumpPositions = isset($modes['positions']);

        $errors = new ErrorHandler\Collecting;
        $stmts = $parser->parse($code, $errors);

        $output = '';
        foreach ($errors->getErrors() as $error) {
            $output .= $this->formatErrorMessage($error, $code) . "\n";
        }

        if (null !== $stmts) {
            $dumper = new NodeDumper(['dumpComments' => true, 'dumpPositions' => $dumpPositions]);
            $output .= $dumper->dump($stmts, $code);
        }

        return [$stmts, canonicalize($output)];
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    private function formatErrorMessage(Error $e, $code) {
        if ($e->hasColumnInfo()) {
            return $e->getMessageWithColumnInfo($code);
        }

        return $e->getMessage();
    }

    private function checkAttributes($stmts) {
        if ($stmts === null) {
            return;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function enterNode(Node $node) {
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
