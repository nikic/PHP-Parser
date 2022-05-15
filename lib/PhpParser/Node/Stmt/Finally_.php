<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

<<<<<<< HEAD
class Finally_ extends Node\Stmt {
=======
class Finally_ extends Node\Stmt implements Node\StmtsIterable
{
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a finally node.
     *
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $stmts = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames(): array {
        return ['stmts'];
    }

<<<<<<< HEAD
    public function getType(): string {
=======
    public function getType() : string {
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
        return 'Stmt_Finally';
    }
}
