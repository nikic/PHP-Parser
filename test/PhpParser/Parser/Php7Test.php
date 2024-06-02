<?php declare(strict_types=1);

namespace PhpParser\Parser;

use PhpParser\Lexer;
use PhpParser\ParserTestAbstract;

class Php7Test extends ParserTestAbstract
{
    protected function getParser(Lexer $lexer) {
        return new Php7($lexer);
    }
}
