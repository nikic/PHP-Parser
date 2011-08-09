<?php

/**
 * @property string $value String value
 */
class PHPParser_Node_Scalar_String extends PHPParser_Node_Scalar
{
    /**
     * Constructs a string scalar node.
     *
     * @param string      $value      Value of the string
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($value = '', $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $line, $docComment
        );
    }

    /**
     * Creates a String node from a string token (parses escape sequences).
     *
     * @param string      $s          String
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     *
     * @return PHPParser_Node_Scalar_String String Node
     */
    public static function create($s, $line, $docComment) {
        $bLength = 0;
        if ('b' === $s[0]) {
            $bLength = 1;
        }

        if ('\'' === $s[$bLength]) {
            $s = str_replace(
                array('\\\\', '\\\''),
                array(  '\\',   '\''),
                substr($s, $bLength + 1, -1)
            );
        } else {
            $s = self::parseEscapeSequences(substr($s, $bLength + 1, -1));
        }

        return new self($s, $line, $docComment);
    }

    /**
     * Parses escape sequences in the content of a doubly quoted string
     * or heredoc string.
     *
     * @param string $s String without quotes
     *
     * @return string String with escape sequences parsed
     */
    public static function parseEscapeSequences($s) {
        // TODO: parse hex and oct escape sequences

        return str_replace(
            array('\\\\', '\"', '\$', '\n', '\r', '\t', '\f', '\v'),
            array(  '\\',  '"',  '$', "\n", "\r", "\t", "\f", "\v"),
            $s
        );
    }
}