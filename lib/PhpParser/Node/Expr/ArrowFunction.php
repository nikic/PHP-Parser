<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class ArrowFunction extends Expr implements FunctionLike
{
    /**
     * @var bool
     */
    public $byRef;

    /**
     * @var Node\Param[]
     */
    public $params = [];

    /**
     * @var null|Node\Identifier|Node\Name|Node\NullableType
     */
    public $returnType;

    /**
     * @var Node\Stmt\Expression
     */
    private $expr;

    public function __construct(array $subNodes = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $returnType = $subNodes['returnType'] ?? null;
        $this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
        $this->expr = $subNodes['expr'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['byRef', 'params', 'returnType', 'expr'];
    }

    public function getParams() : array {
        return $this->params;
    }

    public function returnsByRef() : bool {
        return $this->byRef;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    public function getExpr() : Node\Stmt\Expression
    {
        return $this->expr;
    }

    // @todo required by interface, but not really needed
    /** @return Node\Stmt[] */
    public function getStmts() : array {
        return [$this->expr];
    }

    public function getType() : string {
        return 'Expr_ArrowFunction';
    }
}
