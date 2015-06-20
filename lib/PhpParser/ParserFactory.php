<?php

namespace PhpParser;

class ParserFactory {
    const PREFER_PHP7 = 1;
    const PREFER_PHP5 = 2;
    const ONLY_PHP7 = 3;
    const ONLY_PHP5 = 4;

    /**
     * @param int $kind
     * @return ParserInterface
     */
    public function create($kind) {
        $lexer = new Lexer\Emulative();
        switch ($kind) {
            case self::PREFER_PHP7:
                return new Parser\Multiple([
                    new Parser\Php7($lexer), new Parser\Php5($lexer)
                ]);
            case self::PREFER_PHP5:
                return new Parser\Multiple([
                    new Parser\Php5($lexer), new Parser\Php7($lexer)
                ]);
            case self::ONLY_PHP7:
                return new Parser\Php7($lexer);
            case self::ONLY_PHP5:
                return new Parser\Php5($lexer);
            default:
                throw new \LogicException(
                    'Kind must be one of ::PREFER_PHP7, ::PREFER_PHP5, ::ONLY_PHP7 or ::ONLY_PHP5'
                );
        }
    }
}
