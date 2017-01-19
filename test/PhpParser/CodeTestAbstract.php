<?php

namespace PhpParser;

require_once __DIR__ . '/CodeTestParser.php';

abstract class CodeTestAbstract extends \PHPUnit_Framework_TestCase
{
    protected function getTests($directory, $fileExtension, $chunksPerTest = 2) {
        $parser = new CodeTestParser;
        $allTests = array();
        foreach (filesInDir($directory, $fileExtension) as $fileName => $fileContents) {
            list($name, $tests) = $parser->parseTest($fileContents, $chunksPerTest);

            // first part is the name
            $name .= ' (' . $fileName . ')';
            $shortName = ltrim(str_replace($directory, '', $fileName), '/\\');

            // multiple sections possible with always two forming a pair
            foreach ($tests as $i => list($mode, $parts)) {
                $dataSetName = $shortName . (count($parts) > 1 ? '#' . $i : '');
                $allTests[$dataSetName] = array_merge([$name], $parts, [$mode]);
            }
        }

        return $allTests;
    }
}
