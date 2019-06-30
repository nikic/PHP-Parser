<?php declare(strict_types=1);

namespace PhpParser;

class Token {
    /** @var int Token id (a PhpParser\Parser\Tokens::T_* constant) */
    public $id; // TODO: Move this to PhpParser\Tokens.
    /** @var string Textual value of the token */
    public $value;
    /** @var int Start line number of the token */
    public $line;
    /** @var int Offset of the token in the source code */
    public $filePos;

    public function __construct(int $id, string $value, int $line, int $filePos) {
        $this->id = $id;
        $this->value = $value;
        $this->line = $line;
        $this->filePos = $filePos;
    }
}