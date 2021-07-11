<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;

interface PrettyPrinter
{

    /**
     * Convert the AST into PHP code suitable for execution by eval
     *
     * Note, that this does not include a "<?php" tag at the start
     *
     * @param Node[] $stmts The statements to render
     * 
     * @return string Rendered PHP code
     */
    public function prettyPrint(array $stmts) : string;

    /**
     * Convert a single expression to executable code
     *
     * @param Expr $node The node to render
     *
     * @return string the rendered PHP code for the expression
     */
    public function prettyPrintExpr(Expr $node) : string;

    /**
     * Convert the AST into a string for use as a PHP file
     *
     * This includes the opening <?php necessary
     *
     * @param Node[] $stmts The statements to render
     *
     * @return string The rendered PHP code
     */
    public function prettyPrintFile(array $stmts) : string;

    /**
     * Perform a format-preserving pretty print of an AST.
     *
     * The format preservation is best effort. For some changes to the AST the formatting will not
     * be preserved (at least not locally).
     *
     * In order to use this method a number of prerequisites must be satisfied:
     *  * The startTokenPos and endTokenPos attributes in the lexer must be enabled.
     *  * The CloningVisitor must be run on the AST prior to modification.
     *  * The original tokens must be provided, using the getTokens() method on the lexer.
     *
     * @param Node[] $stmts      Modified AST with links to original AST
     * @param Node[] $origStmts  Original AST with token offset information
     * @param array  $origTokens Tokens of the original code
     *
     * @return string
     */
    public function printFormatPreserving(array $stmts, array $origStmts, array $origTokens) : string;


}