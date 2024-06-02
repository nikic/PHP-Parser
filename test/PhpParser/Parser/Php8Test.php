<?php declare(strict_types=1);

namespace PhpParser\Parser;

use PhpParser\Lexer;
use PhpParser\ParserTestAbstract;

class Php8Test extends ParserTestAbstract
{
    protected function getParser(Lexer $lexer) {
        return new Php8($lexer);
    }
}
