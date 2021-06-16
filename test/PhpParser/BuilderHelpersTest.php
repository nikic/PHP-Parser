<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name;

class BuilderHelpersTest extends \PHPUnit\Framework\TestCase
{
    public function testNormalizeAttribute() {
        $attribute = new Attribute(new Name('Test'));
        $attributeGroup = new AttributeGroup([$attribute]);

        $this->assertEquals($attributeGroup, BuilderHelpers::normalizeAttribute($attribute));
        $this->assertSame($attributeGroup, BuilderHelpers::normalizeAttribute($attributeGroup));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Attribute must be an instance of PhpParser\Node\Attribute or PhpParser\Node\AttributeGroup');
        BuilderHelpers::normalizeAttribute('test');
    }
}
