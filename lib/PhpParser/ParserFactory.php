<?php declare(strict_types=1);

namespace PhpParser;

class ParserFactory
{
    const PREFER_PHP7 = 1;
    const ONLY_PHP7 = 3;

    /**
     * Creates a Parser instance, according to the provided kind.
     *
     * @param int        $kind  One of ::PREFER_PHP7 or ::ONLY_PHP7
     * @param Lexer|null $lexer Lexer to use. Defaults to emulative lexer when not specified
     * @param array      $parserOptions Parser options. See ParserAbstract::__construct() argument
     *
     * @return Parser The parser instance
     */
    public function create(int $kind, Lexer $lexer = null, array $parserOptions = []) : Parser {
        if (null === $lexer) {
            $lexer = new Lexer\Emulative();
        }
        switch ($kind) {
            case self::PREFER_PHP7:
            case self::ONLY_PHP7:
                return new Parser\Php7($lexer, $parserOptions);
            default:
                throw new \LogicException(
                    'Kind must be one of ::PREFER_PHP7 or ::ONLY_PHP7'
                );
        }
    }
}
