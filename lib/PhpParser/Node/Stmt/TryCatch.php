<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class TryCatch extends Node\Stmt
{
    /** @var Node[] Statements */
    public $stmts;
    /** @var Catch_[] Catches */
    public $catches;
    /** @var null|Finally_ Optional finally node */
    public $finally;

    /**
     * Constructs a try catch node.
     *
     * @param Node[]        $stmts      Statements
     * @param Catch_[]      $catches    Catches
     * @param null|Finally_ $finally    Optionaly finally node
     * @param array|null    $attributes Additional attributes
     */
    public function __construct(array $stmts, array $catches, Finally_ $finally = null, array $attributes = array()) {
        if (empty($catches) && null === $finally) {
            throw new Error('Cannot use try without catch or finally');
        }

        parent::__construct($attributes);
        $this->stmts = $stmts;
        $this->catches = $catches;
        $this->finally = $finally;
    }

    public function getSubNodeNames() {
        return array('stmts', 'catches', 'finally');
    }
}
