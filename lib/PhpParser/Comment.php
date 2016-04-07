<?php

namespace PhpParser;

class Comment
{
    protected $text;
    protected $line;
    protected $filePos;

    /**
     * Constructs a comment node.
     *
     * @param string $text         Comment text (including comment delimiters like /*)
     * @param int    $startLine    Line number the comment started on
     * @param int    $startFilePos File offset the comment started on
     */
    public function __construct($text, $startLine = -1, $startFilePos = -1) {
        $this->text = $text;
        $this->line = $startLine;
        $this->filePos = $startFilePos;
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
     *
     * @deprecated Construct a new comment instead
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Gets the line number the comment started on.
     *
     * @return int Line number
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Sets the line number the comment started on.
     *
     * @param int $line Line number
     *
     * @deprecated Construct a new comment instead
     */
    public function setLine($line) {
        $this->line = $line;
    }

    /**
     * Gets the file offset the comment started on.
     *
     * @return int File offset
     */
    public function getFilePos() {
        return $this->filePos;
    }

    /**
     * Gets the comment text.
     *
     * @return string The comment text (including comment delimiters like /*)
     */
    public function __toString() {
        return $this->text;
    }

    /**
     * Gets the reformatted comment text.
     *
     * "Reformatted" here means that we try to clean up the whitespace at the
     * starts of the lines. This is necessary because we receive the comments
     * without trailing whitespace on the first line, but with trailing whitespace
     * on all subsequent lines.
     *
     * @return mixed|string
     */
    public function getReformattedText() {
        $text = trim($this->text);
        $newlinePos = strpos($text, "\n");
        if (false === $newlinePos) {
            // Single line comments don't need further processing
            return $text;
        } elseif (preg_match('((*BSR_ANYCRLF)(*ANYCRLF)^.*(?:\R\s+\*.*)+$)', $text)) {
            // Multi line comment of the type
            //
            //     /*
            //      * Some text.
            //      * Some more text.
            //      */
            //
            // is handled by replacing the whitespace sequences before the * by a single space
            return preg_replace('(^\s+\*)m', ' *', $this->text);
        } elseif (preg_match('(^/\*\*?\s*[\r\n])', $text) && preg_match('(\n(\s*)\*/$)', $text, $matches)) {
            // Multi line comment of the type
            //
            //    /*
            //        Some text.
            //        Some more text.
            //    */
            //
            // is handled by removing the whitespace sequence on the line before the closing
            // */ on all lines. So if the last line is "    */", then "    " is removed at the
            // start of all lines.
            return preg_replace('(^' . preg_quote($matches[1]) . ')m', '', $text);
        } elseif (preg_match('(^/\*\*?\s*(?!\s))', $text, $matches)) {
            // Multi line comment of the type
            //
            //     /* Some text.
            //        Some more text.
            //          Indented text.
            //        Even more text. */
            //
            // is handled by removing the difference between the shortest whitespace prefix on all
            // lines and the length of the "/* " opening sequence.
            $prefixLen = $this->getShortestWhitespacePrefixLen(substr($text, $newlinePos + 1));
            $removeLen = $prefixLen - strlen($matches[0]);
            return preg_replace('(^\s{' . $removeLen . '})m', '', $text);
        }

        // No idea how to format this comment, so simply return as is
        return $text;
    }

    private function getShortestWhitespacePrefixLen($str) {
        $lines = explode("\n", $str);
        $shortestPrefixLen = INF;
        foreach ($lines as $line) {
            preg_match('(^\s*)', $line, $matches);
            $prefixLen = strlen($matches[0]);
            if ($prefixLen < $shortestPrefixLen) {
                $shortestPrefixLen = $prefixLen;
            }
        }
        return $shortestPrefixLen;
    }
}