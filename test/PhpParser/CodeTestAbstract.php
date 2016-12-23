<?php

namespace PhpParser;

abstract class CodeTestAbstract extends \PHPUnit_Framework_TestCase
{
    protected function getTests($directory, $fileExtension, $chunksPerTest = 2) {
        $directory = realpath($directory);
        $it = new \RecursiveDirectoryIterator($directory);
        $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new \RegexIterator($it, '(\.' . preg_quote($fileExtension) . '$)');

        $tests = array();
        foreach ($it as $file) {
            $fileName = $file->getPathname();
            $fileContents = file_get_contents($fileName);
            $fileContents = canonicalize($fileContents);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace_callback(
                '/@@\{(.*?)\}@@/',
                function($matches) {
                    return eval('return ' . $matches[1] . ';');
                },
                $fileContents
            );

            // parse sections
            $parts = preg_split("/\n-----(?:\n|$)/", $fileContents);

            // first part is the name
            $name = array_shift($parts) . ' (' . $fileName . ')';
            $shortName = ltrim(str_replace($directory, '', $fileName), '/\\');

            // multiple sections possible with always two forming a pair
            $chunks = array_chunk($parts, $chunksPerTest);
            foreach ($chunks as $i => $chunk) {
                $dataSetName = $shortName . (count($chunks) > 1 ? '#' . $i : '');
                $lastPart = array_pop($chunk);
                list($lastPart, $mode) = $this->extractMode($lastPart);
                $tests[$dataSetName] = array_merge([$name], $chunk, [$lastPart, $mode]);
            }
        }

        return $tests;
    }

    private function extractMode($expected) {
        $firstNewLine = strpos($expected, "\n");
        if (false === $firstNewLine) {
            $firstNewLine = strlen($expected);
        }

        $firstLine = substr($expected, 0, $firstNewLine);
        if (0 !== strpos($firstLine, '!!')) {
            return [$expected, null];
        }

        $expected = (string) substr($expected, $firstNewLine + 1);
        return [$expected, substr($firstLine, 2)];
    }
}
