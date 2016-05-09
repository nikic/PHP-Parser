<?php

namespace PhpParser\Parser;

use PhpParser\Error;
use PhpParser\Parser;

class Multiple implements Parser {
    /** @var Parser[] List of parsers to try, in order of preference */
    private $parsers;
    /** @var Error[] Errors collected during last parse */
    private $errors;

    /**
     * Create a parser which will try multiple parsers in an order of preference.
     *
     * Parsers will be invoked in the order they're provided to the constructor. If one of the
     * parsers runs without errors, it's output is returned. Otherwise the errors (and
     * PhpParser\Error exception) of the first parser are used.
     *
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers) {
        $this->parsers = $parsers;
        $this->errors = [];
    }

    public function parse($code) {
        list($firstStatements, $firstErrors, $firstError) = $this->tryParse($this->parsers[0], $code);
        if ($firstErrors === []) {
            $this->errors = [];
            return $firstStatements;
        }

        for ($index = 1, $parserCount = count($this->parsers); $index < $parserCount; ++$index) {
            list($statements, $errors) = $this->tryParse($this->parsers[$index], $code);
            if ($errors === []) {
                $this->errors = [];
                return $statements;
            }
        }

        $this->errors = $firstErrors;
        if ($firstError) {
            throw $firstError;
        }
        return $firstStatements;
    }

    public function getErrors() {
        return $this->errors;
    }

    private function tryParse(Parser $parser, $code) {
        $statements = null;
        $error = null;
        try {
            $statements = $parser->parse($code);
        } catch (Error $error) {}
        $errors = $parser->getErrors();
        return [$statements, $errors, $error];
    }
}
