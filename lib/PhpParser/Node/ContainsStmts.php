<?php

declare(strict_types=1);

namespace PhpParser\Node;

interface ContainsStmts {
    /**
     * @return Stmt[]
     */
    public function getStmts(): array;
}
