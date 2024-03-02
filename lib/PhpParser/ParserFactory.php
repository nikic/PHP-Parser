<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Php7;

class ParserFactory
{
    const PREFER_PHP7 = 1;
    const PREFER_PHP5 = 2;
    const ONLY_PHP7 = 3;
    const ONLY_PHP5 = 4;

    /**
     * Creates a Parser instance, according to the provided kind.
     *
     * @param int        $kind  One of ::PREFER_PHP7, ::PREFER_PHP5, ::ONLY_PHP7 or ::ONLY_PHP5
     * @param Lexer|null $lexer Lexer to use. Defaults to emulative lexer when not specified
     * @param array      $parserOptions Parser options. See ParserAbstract::__construct() argument
     *
     * @return Parser The parser instance
     */
    public function create(int $kind, ?Lexer $lexer = null, array $parserOptions = []) : Parser {
        if (null === $lexer) {
            $lexer = new Lexer\Emulative();
        }
        switch ($kind) {
            case self::PREFER_PHP7:
                return new Parser\Multiple([
                    new Parser\Php7($lexer, $parserOptions), new Parser\Php5($lexer, $parserOptions)
                ]);
            case self::PREFER_PHP5:
                return new Parser\Multiple([
                    new Parser\Php5($lexer, $parserOptions), new Parser\Php7($lexer, $parserOptions)
                ]);
            case self::ONLY_PHP7:
                return new Parser\Php7($lexer, $parserOptions);
            case self::ONLY_PHP5:
                return new Parser\Php5($lexer, $parserOptions);
            default:
                throw new \LogicException(
                    'Kind must be one of ::PREFER_PHP7, ::PREFER_PHP5, ::ONLY_PHP7 or ::ONLY_PHP5'
                );
        }
    }

    /**
     * Create a parser targeting the newest version supported by this library. Code for older
     * versions will be accepted if there have been no relevant backwards-compatibility breaks in
     * PHP.
     *
     * All supported lexer attributes (comments, startLine, endLine, startTokenPos, endTokenPos,
     * startFilePos, endFilePos) will be enabled.
     */
    public function createForNewestSupportedVersion(): Parser {
        return new Php7(new Emulative($this->getLexerOptions()));
    }

    /**
     * Create a parser targeting the host PHP version, that is the PHP version we're currently
     * running on. This parser will not use any token emulation.
     *
     * All supported lexer attributes (comments, startLine, endLine, startTokenPos, endTokenPos,
     * startFilePos, endFilePos) will be enabled.
     */
    public function createForHostVersion(): Parser {
        return new Php7(new Lexer($this->getLexerOptions()));
    }

    private function getLexerOptions(): array {
        return ['usedAttributes' => [
            'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos', 'startFilePos', 'endFilePos',
        ]];
    }
}
