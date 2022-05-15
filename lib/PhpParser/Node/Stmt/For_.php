<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

<<<<<<< HEAD
class For_ extends Node\Stmt {
=======
class For_ extends Node\Stmt implements Node\StmtsIterable
{
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
    /** @var Node\Expr[] Init expressions */
    public $init;
    /** @var Node\Expr[] Loop conditions */
    public $cond;
    /** @var Node\Expr[] Loop expressions */
    public $loop;
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a for loop node.
     *
     * @param array $subNodes   Array of the following optional subnodes:
     *                          'init'  => array(): Init expressions
     *                          'cond'  => array(): Loop conditions
     *                          'loop'  => array(): Loop expressions
     *                          'stmts' => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->init = $subNodes['init'] ?? [];
        $this->cond = $subNodes['cond'] ?? [];
        $this->loop = $subNodes['loop'] ?? [];
        $this->stmts = $subNodes['stmts'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['init', 'cond', 'loop', 'stmts'];
    }

<<<<<<< HEAD
    public function getType(): string {
=======
    public function getType() : string {
>>>>>>> 920aae4f (add StmtsIterable interface to mark nodes that contain iterable stmts to improve hooking in node visitors)
        return 'Stmt_For';
    }
}
