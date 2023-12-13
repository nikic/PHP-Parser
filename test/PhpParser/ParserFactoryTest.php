<?php declare(strict_types=1);

namespace PhpParser;

/* This test is very weak, because PHPUnit's assertEquals assertion is way too slow dealing with the
 * large objects involved here. So we just do some basic instanceof tests instead. */

use PhpParser\Node\Stmt\Echo_;

class ParserFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider provideTestCreate */
    public function testCreate($kind, $lexer, $expected) {
        $this->assertInstanceOf($expected, (new ParserFactory)->create($kind, $lexer));
    }

    public function provideTestCreate() {
        $lexer = new Lexer();
        return [
            [
                ParserFactory::PREFER_PHP7, $lexer,
                Parser\Multiple::class
            ],
            [
                ParserFactory::PREFER_PHP5, null,
                Parser\Multiple::class
            ],
            [
                ParserFactory::ONLY_PHP7, null,
                Parser\Php7::class
            ],
            [
                ParserFactory::ONLY_PHP5, $lexer,
                Parser\Php5::class
            ]
        ];
    }

    /** @dataProvider provideTestLexerAttributes */
    public function testLexerAttributes(Parser $parser) {
        $stmts = $parser->parse("<?php /* Bar */ echo 'Foo';");
        $stmt = $stmts[0];
        $this->assertInstanceOf(Echo_::class, $stmt);
        $this->assertCount(1, $stmt->getComments());
        $this->assertSame(1, $stmt->getStartLine());
        $this->assertSame(1, $stmt->getEndLine());
        $this->assertSame(3, $stmt->getStartTokenPos());
        $this->assertSame(6, $stmt->getEndTokenPos());
        $this->assertSame(16, $stmt->getStartFilePos());
        $this->assertSame(26, $stmt->getEndFilePos());
    }

    public function provideTestLexerAttributes() {
        $factory = new ParserFactory();
        return [
            [$factory->createForHostVersion()],
            [$factory->createForNewestSupportedVersion()],
        ];
    }
}
