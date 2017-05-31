<?php

namespace PhpParser\NodeVisitor;

use PhpParser;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

/**
 * Class ScopeResolverTest
 *
 * @author Michael Yoo <michael@yoo.id.au>
 */
class ScopeResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpParser\NodeVisitor\NameResolver
     * @dataProvider provideTestResolveScopes
     */
    public function testResolveScopes($namespace, $code, $result)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser;

        $traverser->addVisitor(new ScopeResolver);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        /** @var Namespace_ $namespaceNode */
        $namespaceNode = $stmts[0];

        //var_export($this->parseScopesRecursive($namespaceNode->stmts));

        $this->assertNotNull(
            $namespaceNode->getAttribute("scope"), "Attribute 'scope' does not exist! Is the test properly written?");
        $this->assertEquals(
            $namespace, $namespaceNode->stmts[0]->getAttribute("scope"), "Namespace node scope does not equal expected");

        $this->assertEquals(
            $result, $this->parseScopesRecursive($namespaceNode->stmts), "Expected scopes result mismatch");
    }

    public function provideTestResolveScopes()
    {
        return [
            [
                "namespace" => "\\",
                "code" => "<?php\n\nnamespace {\n".self::CODE."\n}",
                "result" => $this->resultTestResolveScopes("\\")
            ],
            [
                "namespace" => "\\Vendor\\",
                "code" => "<?php\n\nnamespace Vendor {\n".self::CODE."\n}",
                "result" => $this->resultTestResolveScopes("\\Vendor\\")
            ],
            [
                "namespace" => "\\Vendor\\Package\\",
                "code" => "<?php\n\nnamespace Vendor\\Package {\n".self::CODE."\n}",
                "result" => $this->resultTestResolveScopes("\\Vendor\\Package\\")
            ],
        ];
    }

    public function resultTestResolveScopes($namespace)
    {
        return [
            0 =>
                [
                    'type' => 'Stmt_Use4',
                    'scope' => $namespace,
                ],
            1 =>
                [
                    'type' => 'Stmt_UseUse4',
                    'scope' => $namespace,
                ],
            2 =>
                [
                    'type' => 'Expr_Assign6',
                    'scope' => $namespace,
                ],
            3 =>
                [
                    'type' => 'Stmt_Function8',
                    'scope' => $namespace,
                ],
            4 =>
                [
                    'type' => 'Stmt_Return10',
                    'scope' => $namespace.'test()',
                ],
            5 =>
                [
                    'type' => 'Expr_Closure10',
                    'scope' => $namespace.'test()',
                ],
            6 =>
                [
                    'type' => 'Stmt_Return11',
                    'scope' => $namespace.'test()::Closure',
                ],
            7 =>
                [
                    'type' => 'Scalar_String11',
                    'scope' => $namespace.'test()::Closure',
                ],
            8 =>
                [
                    'type' => 'Stmt_Function15',
                    'scope' => $namespace,
                ],
            9 =>
                [
                    'type' => 'Stmt_Return17',
                    'scope' => $namespace.'nested()',
                ],
            10 =>
                [
                    'type' => 'Expr_Closure17',
                    'scope' => $namespace.'nested()',
                ],
            11 =>
                [
                    'type' => 'Stmt_Return18',
                    'scope' => $namespace.'nested()::Closure',
                ],
            12 =>
                [
                    'type' => 'Expr_Closure18',
                    'scope' => $namespace.'nested()::Closure',
                ],
            13 =>
                [
                    'type' => 'Stmt_Return19',
                    'scope' => $namespace.'nested()::Closure::Closure',
                ],
            14 =>
                [
                    'type' => 'Expr_Closure19',
                    'scope' => $namespace.'nested()::Closure::Closure',
                ],
            15 =>
                [
                    'type' => 'Stmt_Return20',
                    'scope' => $namespace.'nested()::Closure::Closure::Closure',
                ],
            16 =>
                [
                    'type' => 'Scalar_String20',
                    'scope' => $namespace.'nested()::Closure::Closure::Closure',
                ],
            17 =>
                [
                    'type' => 'Stmt_Class26',
                    'scope' => $namespace,
                ],
            18 =>
                [
                    'type' => 'Stmt_ClassMethod28',
                    'scope' => $namespace.'TestClass',
                ],
            19 =>
                [
                    'type' => 'Stmt_Return30',
                    'scope' => $namespace.'TestClass->coverage()',
                ],
            20 =>
                [
                    'type' => 'Expr_FuncCall30',
                    'scope' => $namespace.'TestClass->coverage()',
                ],
            21 =>
                [
                    'type' => 'Arg30',
                    'scope' => $namespace.'TestClass->coverage()',
                ],
            22 =>
                [
                    'type' => 'Expr_Closure30',
                    'scope' => $namespace.'TestClass->coverage()',
                ],
            23 =>
                [
                    'type' => 'Stmt_Return31',
                    'scope' => $namespace.'TestClass->coverage()::Closure',
                ],
            24 =>
                [
                    'type' => 'Scalar_String31',
                    'scope' => $namespace.'TestClass->coverage()::Closure',
                ],
            25 =>
                [
                    'type' => 'Stmt_ClassMethod35',
                    'scope' => $namespace.'TestClass',
                ],
            26 =>
                [
                    'type' => 'Stmt_Return37',
                    'scope' => $namespace.'TestClass->vw()',
                ],
            27 =>
                [
                    'type' => 'Scalar_String37',
                    'scope' => $namespace.'TestClass->vw()',
                ],
            28 =>
                [
                    'type' => 'Stmt_Class41',
                    'scope' => $namespace,
                ],
        ];
    }

    const CODE = <<<'NEWDOC'
use Exception;

$x = "PHP rocks!";

function test()
{
    return function () {
        return "Hello, world!";
    };
}

function nested()
{
    return function () {
        return function () {
            return function () {
                return "Nested nested nested...";
            };
        };
    };
}

class TestClass
{
    public function coverage()
    {
        return call_user_func(function () {
            return "Coverage is 101%!";
        });
    }

    public function vw()
    {
        return "OK (488 tests, 1134 assertions)";
    }
}

class TestException extends Exception {}
NEWDOC;

    /**
     * @param Node[] $nodes
     *
     * @return array
     */
    public function parseScopesRecursive(array $nodes)
    {
        $output = [];

        foreach($nodes as $node)
        {
            $output[] = [
                "type" => $node->getType().$node->getLine(),
                "scope" => $node->getAttribute("scope")
            ];

            if($node instanceof Stmt\Function_
                or $node instanceof Expr\Closure)
            {
                $output = array_merge($output, $this->parseScopesRecursive($node->getStmts()));
            }
            elseif($node instanceof Stmt\Use_ and isset($node->uses))
            {
                $output = array_merge($output, $this->parseScopesRecursive($node->uses));
            }
            elseif($node instanceof Stmt\Return_)
            {
                $output = array_merge($output, $this->parseScopesRecursive([$node->expr]));
            }
            elseif($node instanceof Stmt\Class_
                or $node instanceof Stmt\ClassMethod)
            {
                $output = array_merge($output, $this->parseScopesRecursive($node->stmts));
            }
            elseif($node instanceof Expr\FuncCall)
            {
                $output = array_merge($output, $this->parseScopesRecursive($node->args));
            }
            elseif($node instanceof Node\Arg)
            {
                $output = array_merge($output, $this->parseScopesRecursive([$node->value]));
            }
        }

        return $output;
    }
}