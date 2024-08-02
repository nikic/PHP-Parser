<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Modifiers;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;

class TraitUseAdaptationTest extends \PHPUnit\Framework\TestCase {
    protected function createTraitUseAdaptationBuilder($trait, $method) {
        return new TraitUseAdaptation($trait, $method);
    }

    public function testAsMake(): void {
        $builder = $this->createTraitUseAdaptationBuilder(null, 'foo');

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', null, 'bar'),
            (clone $builder)->as('bar')->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Modifiers::PUBLIC, null),
            (clone $builder)->makePublic()->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Modifiers::PROTECTED, null),
            (clone $builder)->makeProtected()->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Modifiers::PRIVATE, null),
            (clone $builder)->makePrivate()->getNode()
        );
    }

    public function testInsteadof(): void {
        $node = $this->createTraitUseAdaptationBuilder('SomeTrait', 'foo')
            ->insteadof('AnotherTrait')
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Precedence(
                new Name('SomeTrait'),
                'foo',
                [new Name('AnotherTrait')]
            ),
            $node
        );
    }

    public function testAsOnNotAlias(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot set alias for not alias adaptation buider');
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->insteadof('AnotherTrait')
            ->as('bar')
        ;
    }

    public function testInsteadofOnNotPrecedence(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot add overwritten traits for not precedence adaptation buider');
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->as('bar')
            ->insteadof('AnotherTrait')
        ;
    }

    public function testInsteadofWithoutTrait(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Precedence adaptation must have trait');
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->insteadof('AnotherTrait')
        ;
    }

    public function testMakeOnNotAlias(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot set access modifier for not alias adaptation buider');
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->insteadof('AnotherTrait')
            ->makePublic()
        ;
    }

    public function testMultipleMake(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Multiple access type modifiers are not allowed');
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->makePrivate()
            ->makePublic()
        ;
    }

    public function testUndefinedType(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type of adaptation is not defined');
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->getNode()
        ;
    }
}
