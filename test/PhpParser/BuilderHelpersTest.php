<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Builder\Class_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

class BuilderHelpersTest extends \PHPUnit\Framework\TestCase
{
    public function testNormalizeNode() {
        $builder = new Class_('SomeClass');
        $this->assertEquals($builder->getNode(), BuilderHelpers::normalizeNode($builder));

        $attribute = new Node\Attribute(new Node\Name('Test'));
        $this->assertSame($attribute, BuilderHelpers::normalizeNode($attribute));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected node or builder object');
        BuilderHelpers::normalizeNode('test');
    }

    public function testNormalizeStmt() {
        $stmt = new Node\Stmt\Class_('Class');
        $this->assertSame($stmt, BuilderHelpers::normalizeStmt($stmt));

        $expr = new Expr\Variable('fn');
        $normalizedExpr = BuilderHelpers::normalizeStmt($expr);
        $this->assertEquals(new Stmt\Expression($expr), $normalizedExpr);
        $this->assertSame($expr, $normalizedExpr->expr);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected statement or expression node');
        BuilderHelpers::normalizeStmt(new Node\Attribute(new Node\Name('Test')));
    }

    public function testNormalizeStmtInvalidType() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected node or builder object');
        BuilderHelpers::normalizeStmt('test');
    }

    public function testNormalizeIdentifier() {
        $identifier = new Node\Identifier('fn');
        $this->assertSame($identifier, BuilderHelpers::normalizeIdentifier($identifier));
        $this->assertEquals($identifier, BuilderHelpers::normalizeIdentifier('fn'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected string or instance of Node\Identifier');
        BuilderHelpers::normalizeIdentifier(1);
    }

    public function testNormalizeIdentifierOrExpr() {
        $identifier = new Node\Identifier('fn');
        $this->assertSame($identifier, BuilderHelpers::normalizeIdentifierOrExpr($identifier));

        $expr = new Expr\Variable('fn');
        $this->assertSame($expr, BuilderHelpers::normalizeIdentifierOrExpr($expr));
        $this->assertEquals($identifier, BuilderHelpers::normalizeIdentifierOrExpr('fn'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected string or instance of Node\Identifier');
        BuilderHelpers::normalizeIdentifierOrExpr(1);
    }

    public function testNormalizeName() {
        $name = new Node\Name('test');
        $this->assertSame($name, BuilderHelpers::normalizeName($name));
        $this->assertEquals(
            new Node\Name\FullyQualified(['Namespace', 'Test']),
            BuilderHelpers::normalizeName('\\Namespace\\Test')
        );
        $this->assertEquals(
            new Node\Name\Relative(['Test']),
            BuilderHelpers::normalizeName('namespace\\Test')
        );
        $this->assertEquals($name, BuilderHelpers::normalizeName('test'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        BuilderHelpers::normalizeName('');
    }

    public function testNormalizeNameInvalidType() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name must be a string or an instance of Node\Name');
        BuilderHelpers::normalizeName(1);
    }

    public function testNormalizeNameOrExpr() {
        $expr = new Expr\Variable('fn');
        $this->assertSame($expr, BuilderHelpers::normalizeNameOrExpr($expr));

        $name = new Node\Name('test');
        $this->assertSame($name, BuilderHelpers::normalizeNameOrExpr($name));
        $this->assertEquals(
            new Node\Name\FullyQualified(['Namespace', 'Test']),
            BuilderHelpers::normalizeNameOrExpr('\\Namespace\\Test')
        );
        $this->assertEquals(
            new Node\Name\Relative(['Test']),
            BuilderHelpers::normalizeNameOrExpr('namespace\\Test')
        );
        $this->assertEquals($name, BuilderHelpers::normalizeNameOrExpr('test'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        BuilderHelpers::normalizeNameOrExpr('');
    }

    public function testNormalizeNameOrExpInvalidType() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name must be a string or an instance of Node\Name or Node\Expr');
        BuilderHelpers::normalizeNameOrExpr(1);
    }

    public function testNormalizeType() {
        $this->assertEquals(new Node\Identifier('array'), BuilderHelpers::normalizeType('array'));
        $this->assertEquals(new Node\Identifier('callable'), BuilderHelpers::normalizeType('callable'));
        $this->assertEquals(new Node\Identifier('string'), BuilderHelpers::normalizeType('string'));
        $this->assertEquals(new Node\Identifier('int'), BuilderHelpers::normalizeType('int'));
        $this->assertEquals(new Node\Identifier('float'), BuilderHelpers::normalizeType('float'));
        $this->assertEquals(new Node\Identifier('bool'), BuilderHelpers::normalizeType('bool'));
        $this->assertEquals(new Node\Identifier('iterable'), BuilderHelpers::normalizeType('iterable'));
        $this->assertEquals(new Node\Identifier('void'), BuilderHelpers::normalizeType('void'));
        $this->assertEquals(new Node\Identifier('object'), BuilderHelpers::normalizeType('object'));
        $this->assertEquals(new Node\Identifier('mixed'), BuilderHelpers::normalizeType('mixed'));

        $intIdentifier = new Node\Identifier('int');
        $this->assertSame($intIdentifier, BuilderHelpers::normalizeType($intIdentifier));

        $intName = new Node\Name('int');
        $this->assertSame($intName, BuilderHelpers::normalizeType($intName));

        $intNullable = new Node\NullableType('int');
        $this->assertSame($intNullable, BuilderHelpers::normalizeType($intNullable));

        $unionType = new Node\UnionType([new Node\Identifier('int'), new Node\Identifier('string')]);
        $this->assertSame($unionType, BuilderHelpers::normalizeType($unionType));

        $expectedNullable = new Node\NullableType($intIdentifier);
        $nullable = BuilderHelpers::normalizeType('?int');
        $this->assertEquals($expectedNullable, $nullable);
        $this->assertEquals($intIdentifier, $nullable->type);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type must be a string, or an instance of Name, Identifier, NullableType or UnionType');
        BuilderHelpers::normalizeType(1);
    }

    public function testNormalizeTypeNullableVoid() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('void type cannot be nullable');
        BuilderHelpers::normalizeType('?void');
    }

    public function testNormalizeTypeNullableMixed() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('mixed type cannot be nullable');
        BuilderHelpers::normalizeType('?mixed');
    }

    public function testNormalizeValue() {
        $expression = new Scalar\LNumber(1);
        $this->assertSame($expression, BuilderHelpers::normalizeValue($expression));

        $this->assertEquals(new Expr\ConstFetch(new Node\Name('null')), BuilderHelpers::normalizeValue(null));
        $this->assertEquals(new Expr\ConstFetch(new Node\Name('true')), BuilderHelpers::normalizeValue(true));
        $this->assertEquals(new Expr\ConstFetch(new Node\Name('false')), BuilderHelpers::normalizeValue(false));
        $this->assertEquals(new Scalar\LNumber(2), BuilderHelpers::normalizeValue(2));
        $this->assertEquals(new Scalar\DNumber(2.5), BuilderHelpers::normalizeValue(2.5));
        $this->assertEquals(new Scalar\String_('text'), BuilderHelpers::normalizeValue('text'));
        $this->assertEquals(
            new Expr\Array_([
                new Expr\ArrayItem(new Scalar\LNumber(0)),
                new Expr\ArrayItem(new Scalar\LNumber(1), new Scalar\String_('test')),
            ]),
            BuilderHelpers::normalizeValue([
                0,
                'test' => 1,
            ])
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid value');
        BuilderHelpers::normalizeValue(new \stdClass());
    }

    public function testNormalizeDocComment() {
        $docComment = new Comment\Doc('Some doc comment');
        $this->assertSame($docComment, BuilderHelpers::normalizeDocComment($docComment));

        $this->assertEquals($docComment, BuilderHelpers::normalizeDocComment('Some doc comment'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Doc comment must be a string or an instance of PhpParser\Comment\Doc');
        BuilderHelpers::normalizeDocComment(1);
    }

    public function testNormalizeAttribute() {
        $attribute = new Node\Attribute(new Node\Name('Test'));
        $attributeGroup = new Node\AttributeGroup([$attribute]);

        $this->assertEquals($attributeGroup, BuilderHelpers::normalizeAttribute($attribute));
        $this->assertSame($attributeGroup, BuilderHelpers::normalizeAttribute($attributeGroup));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Attribute must be an instance of PhpParser\Node\Attribute or PhpParser\Node\AttributeGroup');
        BuilderHelpers::normalizeAttribute('test');
    }
}
