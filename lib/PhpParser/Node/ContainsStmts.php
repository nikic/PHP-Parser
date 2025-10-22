<?php

declare(strict_types=1);

namespace PhpParser\Node;

/**
 * @property Stmt[]|null $stmts
 */
interface ContainsStmts extends \PhpParser\Node {
    /**
     * @return Stmt[]
     */
    public function getStmts(): array;
}
