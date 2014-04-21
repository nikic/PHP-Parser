<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property null|Node\Name $name  Name
 * @property Node[]         $stmts Statements
 */
class Namespace_ extends Node\Stmt
{
    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a namespace node.
     *
     * @param null|Node\Name $name       Name
     * @param Node[]         $stmts      Statements
     * @param array          $attributes Additional attributes
     */
    public function __construct(Node\Name $name = null, $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name,
                'stmts' => $stmts,
            ),
            $attributes
        );

        if (isset(self::$specialNames[(string) $this->name])) {
            throw new Error(sprintf('Cannot use \'%s\' as namespace name', $this->name));
        }

        if (null !== $this->stmts) {
            foreach ($this->stmts as $stmt) {
                if ($stmt instanceof self) {
                    throw new Error('Namespace declarations cannot be nested', $stmt->getLine());
                }
            }
        }
    }
}
