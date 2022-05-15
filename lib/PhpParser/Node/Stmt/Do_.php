<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

<<<<<<< HEAD
class Do_ extends Node\Stmt {
=======
class Do_ extends Node\Stmt implements Node\StmtsIterable
{
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /** @var Node\Expr Condition */
    public $cond;

    /**
     * Constructs a do while node.
     *
     * @param Node\Expr   $cond       Condition
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $stmts = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames(): array {
        return ['stmts', 'cond'];
    }

<<<<<<< HEAD
    public function getType(): string {
=======
    public function getType() : string {
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
        return 'Stmt_Do';
    }
}
