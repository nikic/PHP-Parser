<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

class Assign extends AssignOp
{
    public function getType() : string {
        return 'Expr_Assign';
    }
}
