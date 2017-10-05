<?php declare(strict_types=1);

namespace PhpParser;

interface PrettyPrinter
{

    /**
     * Convert the AST into PHP code suitable for execution
     *
     * @param Node[] $stmts The statements to render
     * 
     * @return string Rendered PHP code
     */
    public function prettyPrint(array $stmts) : string;

}