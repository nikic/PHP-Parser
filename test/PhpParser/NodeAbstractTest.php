<?php declare(strict_types=1);

namespace PhpParser;

class DummyNode extends NodeAbstract
{
    public $subNode1;
    public $subNode2;
    public $notSubNode;

    public function __construct($subNode1, $subNode2, $notSubNode, $attributes) {
        parent::__construct($attributes);
        $this->subNode1 = $subNode1;
        $this->subNode2 = $subNode2;
        $this->notSubNode = $notSubNode;
    }

    public function getSubNodeNames() : array {
        return ['subNode1', 'subNode2'];
    }

    // This method is only overwritten because the node is located in an unusual namespace
    public function getType() : string {
        return 'Dummy';
    }
}

class NodeAbstractTest extends \PHPUnit\Framework\TestCase
{
    public function provideNodes() {
        $attributes = [
            'startLine' => 10,
            'endLine' => 11,
            'startTokenPos' => 12,
            'endTokenPos' => 13,
            'startFilePos' => 14,
            'endFilePos' => 15,
            'comments'  => [
                new Comment('// Comment 1' . "\n"),
                new Comment\Doc('/** doc comment */'),
                new Comment('// Comment 2' . "\n"),
            ],
        ];

        $node = new DummyNode('value1', 'value2', 'value3', $attributes);

        return [
            [$attributes, $node],
        ];
    }

    /**
     * @dataProvider provideNodes
     */
    public function testConstruct(array $attributes, Node $node) {
        $this->assertSame('Dummy', $node->getType());
        $this->assertSame(['subNode1', 'subNode2'], $node->getSubNodeNames());
        $this->assertSame(10, $node->getLine());
        $this->assertSame(10, $node->getStartLine());
        $this->assertSame(11, $node->getEndLine());
        $this->assertSame(12, $node->getStartTokenPos());
        $this->assertSame(13, $node->getEndTokenPos());
        $this->assertSame(14, $node->getStartFilePos());
        $this->assertSame(15, $node->getEndFilePos());
        $this->assertSame('/** doc comment */', $node->getDocComment()->getText());
        $this->assertSame('value1', $node->subNode1);
        $this->assertSame('value2', $node->subNode2);
        $this->assertObjectHasAttribute('subNode1', $node);
        $this->assertObjectHasAttribute('subNode2', $node);
        $this->assertObjectNotHasAttribute('subNode3', $node);
        $this->assertSame($attributes, $node->getAttributes());
        $this->assertSame($attributes['comments'], $node->getComments());

        return $node;
    }

    /**
     * @dataProvider provideNodes
     */
    public function testGetDocComment(array $attributes, Node $node) {
        $this->assertSame('/** doc comment */', $node->getDocComment()->getText());
        $comments = $node->getComments();

        array_splice($comments, 1, 1, []); // remove doc comment
        $node->setAttribute('comments', $comments);
        $this->assertNull($node->getDocComment());

        // Remove all comments.
        $node->setAttribute('comments', []);
        $this->assertNull($node->getDocComment());
    }

    public function testSetDocComment() {
        $node = new DummyNode(null, null, null, []);

        // Add doc comment to node without comments
        $docComment = new Comment\Doc('/** doc */');
        $node->setDocComment($docComment);
        $this->assertSame($docComment, $node->getDocComment());

        // Replace it
        $docComment = new Comment\Doc('/** doc 2 */');
        $node->setDocComment($docComment);
        $this->assertSame($docComment, $node->getDocComment());

        // Add docmment to node with other comments
        $c1 = new Comment('/* foo */');
        $c2 = new Comment('/* bar */');
        $docComment = new Comment\Doc('/** baz */');
        $node->setAttribute('comments', [$c1, $c2]);
        $node->setDocComment($docComment);
        $this->assertSame([$c1, $c2, $docComment], $node->getAttribute('comments'));

        // Replace doc comment that is not at the end.
        $newDocComment = new Comment\Doc('/** new baz */');
        $node->setAttribute('comments', [$c1, $docComment, $c2]);
        $node->setDocComment($newDocComment);
        $this->assertSame([$c1, $newDocComment, $c2], $node->getAttribute('comments'));
    }

    /**
     * @dataProvider provideNodes
     */
    public function testChange(array $attributes, DummyNode $node) {
        // direct modification
        $node->subNode1 = 'newValue';
        $this->assertSame('newValue', $node->subNode1);

        // indirect modification
        $subNode =& $node->subNode1;
        $subNode = 'newNewValue';
        $this->assertSame('newNewValue', $node->subNode1);

        // removal
        unset($node->subNode1);
        $this->assertFalse(isset($node->subNode1));
    }

    /**
     * @dataProvider provideNodes
     */
    public function testIteration(array $attributes, Node $node) {
        // Iteration is simple object iteration over properties,
        // not over subnodes
        $i = 0;
        foreach ($node as $key => $value) {
            if ($i === 0) {
                $this->assertSame('subNode1', $key);
                $this->assertSame('value1', $value);
            } elseif ($i === 1) {
                $this->assertSame('subNode2', $key);
                $this->assertSame('value2', $value);
            } elseif ($i === 2) {
                $this->assertSame('notSubNode', $key);
                $this->assertSame('value3', $value);
            } else {
                throw new \Exception;
            }
            $i++;
        }
        $this->assertSame(3, $i);
    }

    public function testAttributes() {
        /** @var $node Node */
        $node = $this->getMockForAbstractClass(NodeAbstract::class);

        $this->assertEmpty($node->getAttributes());

        $node->setAttribute('key', 'value');
        $this->assertTrue($node->hasAttribute('key'));
        $this->assertSame('value', $node->getAttribute('key'));

        $this->assertFalse($node->hasAttribute('doesNotExist'));
        $this->assertNull($node->getAttribute('doesNotExist'));
        $this->assertSame('default', $node->getAttribute('doesNotExist', 'default'));

        $node->setAttribute('null', null);
        $this->assertTrue($node->hasAttribute('null'));
        $this->assertNull($node->getAttribute('null'));
        $this->assertNull($node->getAttribute('null', 'default'));

        $this->assertSame(
            [
                'key'  => 'value',
                'null' => null,
            ],
            $node->getAttributes()
        );

        $node->setAttributes(
            [
                'a' => 'b',
                'c' => null,
            ]
        );
        $this->assertSame(
            [
                'a' => 'b',
                'c' => null,
            ],
            $node->getAttributes()
        );
    }

    public function testJsonSerialization() {
        $code = <<<'PHP'
<?php
// comment
/** doc comment */
function functionName(&$a = 0, $b = 1.0) {
    echo 'Foo';
}
PHP;
        $expected = <<<'JSON'
[
    {
        "nodeType": "Stmt_Function",
        "byRef": false,
        "name": {
            "nodeType": "Identifier",
            "name": "functionName",
            "attributes": {
                "startLine": 4,
                "endLine": 4
            }
        },
        "params": [
            {
                "nodeType": "Param",
                "type": null,
                "byRef": true,
                "variadic": false,
                "var": {
                    "nodeType": "Expr_Variable",
                    "name": "a",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4
                    }
                },
                "default": {
                    "nodeType": "Scalar_LNumber",
                    "value": 0,
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4,
                        "rawValue": "0",
                        "kind": 10
                    }
                },
                "flags": 0,
                "attrGroups": [],
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                }
            },
            {
                "nodeType": "Param",
                "type": null,
                "byRef": false,
                "variadic": false,
                "var": {
                    "nodeType": "Expr_Variable",
                    "name": "b",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4
                    }
                },
                "default": {
                    "nodeType": "Scalar_DNumber",
                    "value": 1,
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4,
                        "rawValue": "1.0"
                    }
                },
                "flags": 0,
                "attrGroups": [],
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                }
            }
        ],
        "returnType": null,
        "stmts": [
            {
                "nodeType": "Stmt_Echo",
                "exprs": [
                    {
                        "nodeType": "Scalar_String",
                        "value": "Foo",
                        "attributes": {
                            "startLine": 5,
                            "endLine": 5,
                            "kind": 1,
                            "rawValue": "'Foo'"
                        }
                    }
                ],
                "attributes": {
                    "startLine": 5,
                    "endLine": 5
                }
            }
        ],
        "attrGroups": [],
        "namespacedName": null,
        "attributes": {
            "startLine": 4,
            "comments": [
                {
                    "nodeType": "Comment",
                    "text": "\/\/ comment",
                    "line": 2,
                    "filePos": 6,
                    "tokenPos": 1,
                    "endLine": 2,
                    "endFilePos": 15,
                    "endTokenPos": 1
                },
                {
                    "nodeType": "Comment_Doc",
                    "text": "\/** doc comment *\/",
                    "line": 3,
                    "filePos": 17,
                    "tokenPos": 3,
                    "endLine": 3,
                    "endFilePos": 34,
                    "endTokenPos": 3
                }
            ],
            "endLine": 6
        }
    }
]
JSON;
        $expected81 = <<<'JSON'
[
    {
        "nodeType": "Stmt_Function",
        "attributes": {
            "startLine": 4,
            "comments": [
                {
                    "nodeType": "Comment",
                    "text": "\/\/ comment",
                    "line": 2,
                    "filePos": 6,
                    "tokenPos": 1,
                    "endLine": 2,
                    "endFilePos": 15,
                    "endTokenPos": 1
                },
                {
                    "nodeType": "Comment_Doc",
                    "text": "\/** doc comment *\/",
                    "line": 3,
                    "filePos": 17,
                    "tokenPos": 3,
                    "endLine": 3,
                    "endFilePos": 34,
                    "endTokenPos": 3
                }
            ],
            "endLine": 6
        },
        "byRef": false,
        "name": {
            "nodeType": "Identifier",
            "attributes": {
                "startLine": 4,
                "endLine": 4
            },
            "name": "functionName"
        },
        "params": [
            {
                "nodeType": "Param",
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                },
                "type": null,
                "byRef": true,
                "variadic": false,
                "var": {
                    "nodeType": "Expr_Variable",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4
                    },
                    "name": "a"
                },
                "default": {
                    "nodeType": "Scalar_LNumber",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4,
                        "rawValue": "0",
                        "kind": 10
                    },
                    "value": 0
                },
                "flags": 0,
                "attrGroups": []
            },
            {
                "nodeType": "Param",
                "attributes": {
                    "startLine": 4,
                    "endLine": 4
                },
                "type": null,
                "byRef": false,
                "variadic": false,
                "var": {
                    "nodeType": "Expr_Variable",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4
                    },
                    "name": "b"
                },
                "default": {
                    "nodeType": "Scalar_DNumber",
                    "attributes": {
                        "startLine": 4,
                        "endLine": 4,
                        "rawValue": "1.0"
                    },
                    "value": 1
                },
                "flags": 0,
                "attrGroups": []
            }
        ],
        "returnType": null,
        "stmts": [
            {
                "nodeType": "Stmt_Echo",
                "attributes": {
                    "startLine": 5,
                    "endLine": 5
                },
                "exprs": [
                    {
                        "nodeType": "Scalar_String",
                        "attributes": {
                            "startLine": 5,
                            "endLine": 5,
                            "kind": 1,
                            "rawValue": "'Foo'"
                        },
                        "value": "Foo"
                    }
                ]
            }
        ],
        "attrGroups": [],
        "namespacedName": null
    }
]
JSON;

        if (version_compare(PHP_VERSION, '8.1', '>=')) {
            $expected = $expected81;
        }

        $parser = new Parser\Php7(new Lexer());
        $stmts = $parser->parse(canonicalize($code));
        $json = json_encode($stmts, JSON_PRETTY_PRINT);
        $this->assertEquals(canonicalize($expected), canonicalize($json));
    }
}
