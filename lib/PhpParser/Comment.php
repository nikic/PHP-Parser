<?php declare(strict_types=1);

namespace PhpParser;

class Comment implements \JsonSerializable {
    /** @var string string */
    protected $text;
    /** @var int */
    protected $startLine;
    /** @var int */
    protected $startFilePos;
    /** @var int */
    protected $startTokenPos;
    /** @var int */
    protected $endLine;
    /** @var int */
    protected $endFilePos;
    /** @var int */
    protected $endTokenPos;

    /**
     * Constructs a comment node.
     *
     * @param string $text          Comment text (including comment delimiters like /*)
     * @param int    $startLine     Line number the comment started on
     * @param int    $startFilePos  File offset the comment started on
     * @param int    $startTokenPos Token offset the comment started on
     */
    public function __construct(
        string $text,
        int $startLine = -1, int $startFilePos = -1, int $startTokenPos = -1,
        int $endLine = -1, int $endFilePos = -1, int $endTokenPos = -1
    ) {
        $this->text = $text;
        $this->startLine = $startLine;
        $this->startFilePos = $startFilePos;
        $this->startTokenPos = $startTokenPos;
        $this->endLine = $endLine;
        $this->endFilePos = $endFilePos;
        $this->endTokenPos = $endTokenPos;
    }

    /**
     * Gets the comment text.
     *
     * @return string The comment text (including comment delimiters like /*)
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * Gets the line number the comment started on.
     *
     * @return int Line number (or -1 if not available)
     */
    public function getStartLine(): int {
        return $this->startLine;
    }

    /**
     * Gets the file offset the comment started on.
     *
     * @return int File offset (or -1 if not available)
     */
    public function getStartFilePos(): int {
        return $this->startFilePos;
    }

    /**
     * Gets the token offset the comment started on.
     *
     * @return int Token offset (or -1 if not available)
     */
    public function getStartTokenPos(): int {
        return $this->startTokenPos;
    }

    /**
     * Gets the line number the comment ends on.
     *
     * @return int Line number (or -1 if not available)
     */
    public function getEndLine(): int {
        return $this->endLine;
    }

    /**
     * Gets the file offset the comment ends on.
     *
     * @return int File offset (or -1 if not available)
     */
    public function getEndFilePos(): int {
        return $this->endFilePos;
    }

    /**
     * Gets the token offset the comment ends on.
     *
     * @return int Token offset (or -1 if not available)
     */
    public function getEndTokenPos(): int {
        return $this->endTokenPos;
    }

    /**
     * Gets the line number the comment started on.
     *
     * @deprecated Use getStartLine() instead
     *
     * @return int Line number
     */
    public function getLine(): int {
        return $this->startLine;
    }

    /**
     * Gets the file offset the comment started on.
     *
     * @deprecated Use getStartFilePos() instead
     *
     * @return int File offset
     */
    public function getFilePos(): int {
        return $this->startFilePos;
    }

    /**
     * Gets the token offset the comment started on.
     *
     * @deprecated Use getStartTokenPos() instead
     *
     * @return int Token offset
     */
    public function getTokenPos(): int {
        return $this->startTokenPos;
    }

    /**
     * Gets the comment text.
     *
     * @return string The comment text (including comment delimiters like /*)
     */
    public function __toString(): string {
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
     * @return string
     */
    public function getReformattedText() {
        if (
            $this->startLine !== $this->endLine
            || ($this->endLine === -1 && strpos($this->text, "\n") !== false)
        ) {
            if (preg_match('((*BSR_ANYCRLF)(*ANYCRLF)^.*(?:\R\s+\*.*)+$)', $this->text)) {
                // Multi line comment of the type
                //
                //     /*
                //      * Some text.
                //      * Some more text.
                //      */
                //
                // is handled by replacing the whitespace sequences before the * by a single space
                return (string) preg_replace('(^\s+\*)m', ' *', $this->text);
            }
            if (preg_match('(^/\*+\s*[\r\n])', $this->text) && preg_match('(^(\s*)\*/$)m', $this->text, $matches)) {
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
                return (string) preg_replace('(^' . preg_quote($matches[1]) . ')m', '', $this->text);
            }
            if (preg_match('(^/\*+\s*(?!\s))m', $this->text, $matches)) {
                // Multi line comment of the type
                //
                //     /* Some text.
                //        Some more text.
                //          Indented text.
                //        Even more text. */
                //
                // is handled by removing the difference between the shortest whitespace prefix on all
                // lines and the length of the "/* " opening sequence.
                $prefixLen = $this->getShortestWhitespacePrefixLen(explode("\n", $this->text, 2)[1]);
                $removeLen = $prefixLen - strlen($matches[0]);
                return (string) preg_replace('(^\s{' . $removeLen . '})m', '', $this->text);
            }
        }
        return trim($this->text);
    }

    /**
     * Get length of shortest whitespace prefix (at the start of a line).
     *
     * If there is a line with no prefix whitespace, 0 is a valid return value.
     *
     * @param string $str String to check
     * @return int Length in characters. Tabs count as single characters.
     */
    private function getShortestWhitespacePrefixLen(string $str): int {
        $lines = explode("\n", $str);
        $shortestPrefixLen = \PHP_INT_MAX;
        foreach ($lines as $line) {
            preg_match('(^\s*)m', $line, $matches);
            $prefixLen = strlen($matches[0]);
            if ($prefixLen < $shortestPrefixLen) {
                $shortestPrefixLen = $prefixLen;
            }
        }
        return $shortestPrefixLen;
    }

    /**
     * @psalm-return array{nodeType: string, text: string, line: int, filePos: int, tokenPos: int, endLine: int,
     * endFilePos: int, endTokenPos: int}
     */
    public function jsonSerialize(): array {
        // Technically not a node, but we make it look like one anyway
        return [
            'nodeType' => 'Comment',
            'text' => $this->text,
            // TODO: Rename these to include "start".
            'line' => $this->startLine,
            'filePos' => $this->startFilePos,
            'tokenPos' => $this->startTokenPos,
            'endLine' => $this->endLine,
            'endFilePos' => $this->endFilePos,
            'endTokenPos' => $this->endTokenPos,
        ];
    }
}
