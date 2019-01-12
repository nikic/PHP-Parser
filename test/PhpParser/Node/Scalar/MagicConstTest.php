<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

class MagicConstTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideTestGetName
     */
    public function testGetName(MagicConst $magicConst, $name) {
        $this->assertSame($name, $magicConst->getName());
    }

    public function provideTestGetName(): \Iterator
    {
        yield [new MagicConst\Class_, '__CLASS__'];
        yield [new MagicConst\Dir, '__DIR__'];
        yield [new MagicConst\File, '__FILE__'];
        yield [new MagicConst\Function_, '__FUNCTION__'];
        yield [new MagicConst\Line, '__LINE__'];
        yield [new MagicConst\Method, '__METHOD__'];
        yield [new MagicConst\Namespace_, '__NAMESPACE__'];
        yield [new MagicConst\Trait_, '__TRAIT__'];
    }
}
