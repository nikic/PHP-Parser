<?php

class PHPParser_LexerFile extends PHPParser_Lexer
{
    protected $filename;

    /**
     * Creates a Lexer.
     *
     * @param string $filename
     *
     * @throws PHPParser_Error on lexing errors (unterminated comment or unexpected character)
     */
    public function __construct($filename) {
        $this->filename = $filename;
        parent::__construct(file_get_contents($filename));
    }

    public function getFilename() {
        return $this->filename;
    }

}