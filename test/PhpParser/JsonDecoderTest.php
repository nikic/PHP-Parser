<?php declare(strict_types=1);

namespace PhpParser;

class JsonDecoderTest extends \PHPUnit\Framework\TestCase
{
    public function testRoundTrip() {
        $code = <<<'PHP'
<?php
// comment
/** doc comment */
function functionName(&$a = 0, $b = 1.0) {
    echo 'Foo';
}
PHP;

        $parser = new Parser\Php7(new Lexer());
        $stmts = $parser->parse($code);
        $json = json_encode($stmts);

        $jsonDecoder = new JsonDecoder();
        $decodedStmts = $jsonDecoder->decode($json);
        $this->assertEquals($stmts, $decodedStmts);
    }

    /** @dataProvider provideTestDecodingError */
    public function testDecodingError($json, $expectedMessage) {
        $jsonDecoder = new JsonDecoder();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);
        $jsonDecoder->decode($json);
    }

    public function provideTestDecodingError(): \Iterator
    {
        yield ['???', 'JSON decoding error: Syntax error'];
        yield ['{"nodeType":123}', 'Node type must be a string'];
        yield ['{"nodeType":"Name","attributes":123}', 'Attributes must be an array'];
        yield ['{"nodeType":"Comment"}', 'Comment must have text'];
        yield ['{"nodeType":"xxx"}', 'Unknown node type "xxx"'];
    }
}
