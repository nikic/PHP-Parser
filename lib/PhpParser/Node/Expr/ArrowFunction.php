<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class ArrowFunction extends Expr implements FunctionLike
{
    /**
     * @var Node\Param[]
     */
    public $params;

    /**
     * @var Node\Stmt
     */
    public $stmt;

    public function __construct(array $subNodes = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->params = $subNodes['params'] ?? [];
        $this->stmt = $subNodes['stmt'] ?? null;
    }

    public function getSubNodeNames() : array {
        return ['params', 'stmt'];
    }

    public function getParams() : array {
        return $this->params;
    }

    public function getType() : string {
        return 'Expr_ArrowFunction';
    }

    // required by interface @todo keep?
    public function returnsByRef() : bool {
        return false;
    }

    public function getStmt(): Node\Stmt
    {
        return $this->stmt;
    }

    // required by interface @todo keep?
    public function getReturnType() {
        return null;
    }

    // required by interface @todo keep?
    public function getStmts() : array {
        return $this->stmt;
    }
}
