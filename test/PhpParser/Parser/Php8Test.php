<?php declare(strict_types=1);

namespace PhpParser\Parser;

use PhpParser\Lexer;
use PhpParser\PhpVersionAbstract;

class Php8Test extends PhpVersionAbstract
{
    protected function getParser(Lexer $lexer) {
        return new Php8($lexer);
    }
}
