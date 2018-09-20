<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPUnit\Framework\TestCase;

class TraitUseTest extends TestCase
{
    protected function createTraitUseBuilder(...$traits) {
        return new TraitUse(...$traits);
    }

    public function testAnd() {
        $node = $this->createTraitUseBuilder('SomeTrait')
            ->and('AnotherTrait')
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\TraitUse([
                new Name('SomeTrait'),
                new Name('AnotherTrait')
            ]),
            $node
        );
    }

    public function testWith() {
        $node = $this->createTraitUseBuilder('SomeTrait')
            ->with(new Stmt\TraitUseAdaptation\Alias(null, 'foo', null, 'bar'))
            ->with((new TraitUseAdaptation(null, 'test'))->as('baz'))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\TraitUse([new Name('SomeTrait')], [
                new Stmt\TraitUseAdaptation\Alias(null, 'foo', null, 'bar'),
                new Stmt\TraitUseAdaptation\Alias(null, 'test', null, 'baz')
            ]),
            $node
        );
    }

    public function testInvalidAdaptationNode() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Adaptation must have type TraitUseAdaptation');
        $this->createTraitUseBuilder('Test')
            ->with(new Stmt\Echo_([]))
        ;
    }
}
