<?php

namespace PhpParser;

use PhpParser\Comment;

require_once __DIR__ . '/CodeTestAbstract.php';

class ParserTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $dump) {
        $parser = new Parser(new Lexer\Emulative);
        $dumper = new NodeDumper;

        $stmts = $parser->parse($code);
        $this->assertSame(
            $this->canonicalize($dump),
            $this->canonicalize($dumper->dump($stmts)),
            $name
        );
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    /**
     * @dataProvider provideTestParseFail
     */
    public function testParseFail($name, $code, $msg) {
        $parser = new Parser(new Lexer\Emulative);

        try {
            $parser->parse($code);

            $this->fail(sprintf('"%s": Expected Error', $name));
        } catch (Error $e) {
            $this->assertSame($msg, $e->getMessage(), $name);
        }
    }

    public function provideTestParseFail() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test-fail');
    }

    public function testAttributeAssignment() {
        $lexer = new Lexer(array(
            'usedAttributes' => array(
                'comments', 'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            )
        ));

        $code = <<<'EOC'
<?php
/** Doc comment */
function test($a) {
    // Line
    // Comments
    echo $a;
}
EOC;
        $code = $this->canonicalize($code);

        $parser = new Parser($lexer);
        $stmts = $parser->parse($code);

        /** @var \PhpParser\Node\Stmt\Function_ $fn */
        $fn = $stmts[0];
        $this->assertInstanceOf('PhpParser\Node\Stmt\Function_', $fn);
        $this->assertEquals(array(
            'comments' => array(
                new Comment\Doc('/** Doc comment */', 2),
            ),
            'startLine' => 3,
            'endLine' => 7,
            'startTokenPos' => 3,
            'endTokenPos' => 21,
        ), $fn->getAttributes());

        $param = $fn->params[0];
        $this->assertInstanceOf('PhpParser\Node\Param', $param);
        $this->assertEquals(array(
            'startLine' => 3,
            'endLine' => 3,
            'startTokenPos' => 7,
            'endTokenPos' => 7,
        ), $param->getAttributes());

        /** @var \PhpParser\Node\Stmt\Echo_ $echo */
        $echo = $fn->stmts[0];
        $this->assertInstanceOf('PhpParser\Node\Stmt\Echo_', $echo);
        $this->assertEquals(array(
            'comments' => array(
                new Comment("// Line\n", 4),
                new Comment("// Comments\n", 5),
            ),
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 16,
            'endTokenPos' => 19,
        ), $echo->getAttributes());

        /** @var \PhpParser\Node\Expr\Variable $var */
        $var = $echo->exprs[0];
        $this->assertInstanceOf('PhpParser\Node\Expr\Variable', $var);
        $this->assertEquals(array(
            'startLine' => 6,
            'endLine' => 6,
            'startTokenPos' => 18,
            'endTokenPos' => 18,
        ), $var->getAttributes());
    }
}