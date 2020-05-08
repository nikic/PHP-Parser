<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Attribute;

class Closure extends Expr implements FunctionLike
{
    /** @var bool Whether the closure is static */
    public $static;
    /** @var bool Whether to return by reference */
    public $byRef;
    /** @var Node\Param[] Parameters */
    public $params;
    /** @var ClosureUse[] use()s */
    public $uses;
    /** @var null|Node\Identifier|Node\Name|Node\NullableType|Node\UnionType Return type */
    public $returnType;
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /** @var Attribute[] PHP attributes */
    public $phpAttributes;

    /**
     * Constructs a lambda function node.
     *
     * @param array $subNodes   Array of the following optional subnodes:
     *                          'static'        => false  : Whether the closure is static
     *                          'byRef'         => false  : Whether to return by reference
     *                          'params'        => array(): Parameters
     *                          'uses'          => array(): use()s
     *                          'returnType'    => null   : Return type
     *                          'stmts'         => array(): Statements
     *                          'phpAttributes' => array(): PHP attributes
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->static = $subNodes['static'] ?? false;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $this->uses = $subNodes['uses'] ?? [];
        $returnType = $subNodes['returnType'] ?? null;
        $this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->phpAttributes = $subNodes['phpAttributes'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['phpAttributes', 'static', 'byRef', 'params', 'uses', 'returnType', 'stmts'];
    }

    public function returnsByRef() : bool {
        return $this->byRef;
    }

    public function getParams() : array {
        return $this->params;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    /** @return Node\Stmt[] */
    public function getStmts() : array {
        return $this->stmts;
    }

    public function getPhpAttributes() : array {
        return $this->phpAttributes;
    }

    public function getType() : string {
        return 'Expr_Closure';
    }
}
