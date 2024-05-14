<?php declare(strict_types=1);

namespace PhpParser\Parser;

use PhpParser\Lexer;
use PhpParser\PhpVersionAbstract;

class Php7Test extends PhpVersionAbstract
{
    protected function getParser(Lexer $lexer) {
        return new Php7($lexer);
    }
}
