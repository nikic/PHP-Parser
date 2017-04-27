<?php

namespace PhpParser\Lexer;

class TokenContainer
{
    /**
     * Token Id
     *
     * @var int
     */
    public $id;

    /**
     * Variable to store token content in
     *
     * @var string
     */
    public $value;

    /**
     * Variable to store start attributes in
     *
     * @var array
     */
    public $startAttributes = [];

    /**
     * Variable to store end attributes in
     *
     * @var array
     */
    public $endAttributes = [];
}
