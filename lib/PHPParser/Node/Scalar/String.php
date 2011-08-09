<?php

/**
 * @property string $value    String value
 * @property bool   $isBinary Whether the string is binary (b'')
 */
class PHPParser_Node_Scalar_String extends PHPParser_Node_Scalar
{
    /**
     * Creates a String node from a string token (parses escape sequences).
     *
     * @param string $s    String
     * @param int    $line Line
     *
     * @return PHPParser_Node_Scalar_String String Node
     */
    public static function create($s, $line) {
        $isBinary = false;
        if ('b' === $s[0]) {
            $isBinary = true;
        }

        if ('\'' === $s[$isBinary]) {
            $s = str_replace(
                array('\\\\', '\\\''),
                array(  '\\',   '\''),
                substr($s, $isBinary + 1, -1)
            );
        } else {
            $s = self::parseEscapeSequences(substr($s, $isBinary + 1, -1));
        }

        return new self(
            array(
                'value' => $s, 'isBinary' => $isBinary
            ),
            $line
        );
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