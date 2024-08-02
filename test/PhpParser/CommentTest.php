<?php declare(strict_types=1);

namespace PhpParser;

class CommentTest extends \PHPUnit\Framework\TestCase {
    public function testGetters(): void {
        $comment = new Comment('/* Some comment */',
            1, 10, 2, 1, 27, 2);

        $this->assertSame('/* Some comment */', $comment->getText());
        $this->assertSame('/* Some comment */', (string) $comment);
        $this->assertSame(1, $comment->getStartLine());
        $this->assertSame(10, $comment->getStartFilePos());
        $this->assertSame(2, $comment->getStartTokenPos());
        $this->assertSame(1, $comment->getEndLine());
        $this->assertSame(27, $comment->getEndFilePos());
        $this->assertSame(2, $comment->getEndTokenPos());
    }

    /**
     * @dataProvider provideTestReformatting
     */
    public function testReformatting($commentText, $reformattedText): void {
        $comment = new Comment($commentText);
        $this->assertSame($reformattedText, $comment->getReformattedText());
    }

    public static function provideTestReformatting() {
        return [
            ['// Some text', '// Some text'],
            ['/* Some text */', '/* Some text */'],
            [
                "/**\n     * Some text.\n     * Some more text.\n     */",
                "/**\n * Some text.\n * Some more text.\n */"
            ],
            [
                "/**\r\n     * Some text.\r\n     * Some more text.\r\n     */",
                "/**\n * Some text.\n * Some more text.\n */"
            ],
            [
                "/*\n        Some text.\n        Some more text.\n    */",
                "/*\n    Some text.\n    Some more text.\n*/"
            ],
            [
                "/*\r\n        Some text.\r\n        Some more text.\r\n    */",
                "/*\n    Some text.\n    Some more text.\n*/"
            ],
            [
                "/* Some text.\n       More text.\n       Even more text. */",
                "/* Some text.\n   More text.\n   Even more text. */"
            ],
            [
                "/* Some text.\r\n       More text.\r\n       Even more text. */",
                "/* Some text.\n   More text.\n   Even more text. */"
            ],
            [
                "/* Some text.\n       More text.\n         Indented text. */",
                "/* Some text.\n   More text.\n     Indented text. */",
            ],
            // invalid comment -> no reformatting
            [
                "hello\n    world",
                "hello\n    world",
            ],
        ];
    }
}
