<?php

declare(strict_types=1);

namespace PhpParser\Node;

interface ContainsStmts extends \PhpParser\Node {
    /**
     * @return Stmt[]
     */
    public function getStmts(): array;
}
