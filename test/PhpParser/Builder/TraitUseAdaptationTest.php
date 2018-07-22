<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\TestCase;

class TraitUseAdaptationTest extends TestCase
{
    protected function createTraitUseAdaptationBuilder($trait, $method) {
        return new TraitUseAdaptation($trait, $method);
    }

    public function testAsMake() {
        $builder = $this->createTraitUseAdaptationBuilder(null, 'foo');

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', null, 'bar'),
            (clone $builder)->as('bar')->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Class_::MODIFIER_PUBLIC, null),
            (clone $builder)->makePublic()->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Class_::MODIFIER_PROTECTED, null),
            (clone $builder)->makeProtected()->getNode()
        );

        $this->assertEquals(
            new Stmt\TraitUseAdaptation\Alias(null, 'foo', Class_::MODIFIER_PRIVATE, null),
            (clone $builder)->makePrivate()->getNode()
        );
    }

    public function testInsteadof() {
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

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot set alias for not alias adaptation buider
     */
    public function testAsOnNotAlias() {
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->insteadof('AnotherTrait')
            ->as('bar')
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot add overwritten traits for not precedence adaptation buider
     */
    public function testInsteadofOnNotPrecedence() {
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->as('bar')
            ->insteadof('AnotherTrait')
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Precedence adaptation must have trait
     */
    public function testInsteadofWithoutTrait() {
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->insteadof('AnotherTrait')
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot set access modifier for not alias adaptation buider
     */
    public function testMakeOnNotAlias() {
        $this->createTraitUseAdaptationBuilder('Test', 'foo')
            ->insteadof('AnotherTrait')
            ->makePublic()
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Multiple access type modifiers are not allowed
     */
    public function testMultipleMake() {
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->makePrivate()
            ->makePublic()
        ;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Type of adaptation is not defined
     */
    public function testUndefinedType() {
        $this->createTraitUseAdaptationBuilder(null, 'foo')
            ->getNode()
        ;
    }
}
