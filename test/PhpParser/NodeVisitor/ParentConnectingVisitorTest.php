<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class ParentConnectingVisitorTest extends \PHPUnit\Framework\TestCase
{
    public function testConnectsChildNodeToParentNode()
    {
        $ast = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse(
            '<?php class C { public function m() {} }'
        );

        $traverser = new NodeTraverser;

        $traverser->addVisitor(new ParentConnectingVisitor);

        $ast = $traverser->traverse($ast);

        $traverser = new NodeTraverser;

        $visitor = new FindingVisitor(
            static function(Node $node) {
                return $node instanceof ClassMethod;
            }
        );

        $traverser->addVisitor($visitor);

        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($ast);

        $this->assertSame('C', $visitor->getFoundNodes()[0]->getAttribute('parent')->name->toString());
    }
}
