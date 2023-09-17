<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;

class ParserFactory {
    public const PREFER_PHP7 = 1;
    public const ONLY_PHP7 = 3;

    /**
     * Creates a Parser instance, according to the provided kind.
     *
     * @param int $kind One of ::PREFER_PHP7 or ::ONLY_PHP7
     * @param Lexer|null $lexer Lexer to use. Defaults to emulative lexer when not specified
     *
     * @return Parser The parser instance
     *
     * @deprecated Use createForVersion(), createForNewestSupportedVersion() or createForHostVersion() instead.
     */
    public function create(int $kind, ?Lexer $lexer = null): Parser {
        if (null === $lexer) {
            $lexer = new Lexer\Emulative();
        }
        switch ($kind) {
            case self::PREFER_PHP7:
            case self::ONLY_PHP7:
                return new Parser\Php7($lexer);
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
    public function createForVersion(PhpVersion $version): Parser {
        if ($version->isHostVersion()) {
            $lexer = new Lexer();
        } else {
            $lexer = new Lexer\Emulative($version);
        }
        if ($version->id >= 80000) {
            return new Php8($lexer, $version);
        }
        return new Php7($lexer, $version);
    }

    /**
     * Create a parser targeting the newest version supported by this library. Code for older
     * versions will be accepted if there have been no relevant backwards-compatibility breaks in
     * PHP.
     */
    public function createForNewestSupportedVersion(): Parser {
        return $this->createForVersion(PhpVersion::getNewestSupported());
    }

    /**
     * Create a parser targeting the host PHP version, that is the PHP version we're currently
     * running on. This parser will not use any token emulation.
     */
    public function createForHostVersion(): Parser {
        return $this->createForVersion(PhpVersion::getHostVersion());
    }
}
