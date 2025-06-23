<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

final class StmtsIterableTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $nodes = $parser->parse(<<<'CODE_SAMPLE'
<?php

function clearItemList($items)
{
    foreach ($items as $key => $value) {
        $value = 100;
        $value = 100;
    }
}
CODE_SAMPLE
        );

        $nodeFinder = new NodeFinder();
        $stmtsIterables = $nodeFinder->findInstanceOf($nodes, StmtsIterable::class);

        $this->assertCount(2, $stmtsIterables);
//        $this->assertInstanceOf(Function_::class, $stmtsIterables[0]);
//        $this->assertInstanceOf(Foreach_::class, $stmtsIterables[1]);
    }
}
