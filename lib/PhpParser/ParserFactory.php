<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;

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
     *
     * @deprecated Use createForVersion(), createForNewestSupportedVersion() or createForHostVersion() instead.
     */
    public function create(int $kind, Lexer $lexer = null, array $parserOptions = []): Parser {
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

    /**
     * Create a parser targeting the given version on a best-effort basis. The parser will generally
     * accept code for the newest supported version, but will try to accommodate code that becomes
     * invalid in newer versions or changes in interpretation.
     */
    public function createForVersion(string $version, array $lexerOptions = [], array $parserOptions = []): Parser {
        if ($version === $this->getHostVersion()) {
            $lexer = new Lexer($lexerOptions);
        } else {
            $lexer = new Lexer\Emulative($lexerOptions + ['phpVersion' => $version]);
        }
        if (version_compare($version, '8.0', '>=')) {
            return new Php8($lexer, $parserOptions + ['phpVersion' => $version]);
        }
        return new Php7($lexer, $parserOptions + ['phpVersion' => $version]);
    }

    /**
     * Create a parser targeting the newest version supported by this library. Code for older
     * versions will be accepted if there have been no relevant backwards-compatibility breaks in
     * PHP.
     */
    public function createForNewestSupportedVersion(array $lexerOptions = [], array $parserOptions = []): Parser {
        return $this->createForVersion($this->getNewestSupportedVersion(), $lexerOptions, $parserOptions);
    }

    /**
     * Create a parser targeting the host PHP version, that is the PHP version we're currently
     * running on. This parser will not use any token emulation.
     */
    public function createForHostVersion(array $lexerOptions = [], array $parserOptions = []): Parser {
        return $this->createForVersion($this->getHostVersion(), $lexerOptions, $parserOptions);
    }

    /**
     * Get the newest PHP version supported by this library. Support for this version may be partial,
     * if it is still under development.
     */
    public function getNewestSupportedVersion(): string {
        return '8.2';
    }

    /**
     * Get the host PHP version, that is the PHP version we're currently running on.
     */
    public function getHostVersion(): string {
        return \PHP_MAJOR_VERSION . '.' . \PHP_MINOR_VERSION;
    }
}
