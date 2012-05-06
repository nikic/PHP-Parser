<?php

class PHPParser_Comment
{
    protected $text;

    /**
     * Constructs a comment node.
     *
     * @param string $text Comment text (including comment delimiters like /*)
     */
    public function __construct($text) {
        $this->text = $text;
    }

    /**
     * Gets the comment text.
     *
     * @return string The comment text (including comment delimiters like /*)
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Sets the comment text.
     *
     * @param string $text The comment text (including comment delimiters like /*)
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Gets the comment text.
     *
     * @return string The comment text (including comment delimiters like /*)
     */
    public function __toString() {
        return $this->text;
    }
}