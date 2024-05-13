<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

class MagicConstTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideTestGetName
     */
    public function testGetName(MagicConst $magicConst, $name): void {
        $this->assertSame($name, $magicConst->getName());
    }

    public static function provideTestGetName() {
        return [
            [new MagicConst\Class_(), '__CLASS__'],
            [new MagicConst\Dir(), '__DIR__'],
            [new MagicConst\File(), '__FILE__'],
            [new MagicConst\Function_(), '__FUNCTION__'],
            [new MagicConst\Line(), '__LINE__'],
            [new MagicConst\Method(), '__METHOD__'],
            [new MagicConst\Namespace_(), '__NAMESPACE__'],
            [new MagicConst\Trait_(), '__TRAIT__'],
        ];
    }
}
