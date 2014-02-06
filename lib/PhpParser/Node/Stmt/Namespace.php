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
            throw new Error(sprintf('Cannot use "%s" as namespace name as it is reserved', $this->name));
        }

        if (null !== $this->stmts) {
            foreach ($this->stmts as $stmt) {
                if ($stmt instanceof self) {
                    throw new Error('Namespace declarations cannot be nested', $stmt->getLine());
                }
            }
        }
    }

    public static function postprocess(array $stmts) {
        // null = not in namespace, false = semicolon style, true = bracket style
        $bracketed = null;

        // whether any statements that aren't allowed before a namespace declaration are encountered
        // (the only valid statement currently is a declare)
        $hasNotAllowedStmts = false;

        // offsets for semicolon style namespaces
        // (required for transplanting the following statements into their ->stmts property)
        $nsOffsets = array();

        foreach ($stmts as $i => $stmt) {
            if ($stmt instanceof self) {
                // ->stmts is null if semicolon style is used
                $currentBracketed = null !== $stmt->stmts;

                // if no namespace statement has been encountered yet
                if (!isset($bracketed)) {
                    // set the namespacing style
                    $bracketed = $currentBracketed;

                    // and ensure that it isn't preceded by a not allowed statement
                    if ($hasNotAllowedStmts) {
                        throw new Error('Namespace declaration statement has to be the very first statement in the script', $stmt->getLine());
                    }
                // otherwise ensure that the style of the current namespace matches the style of
                // namespaceing used before in this document
                } elseif ($bracketed !== $currentBracketed) {
                    throw new Error('Cannot mix bracketed namespace declarations with unbracketed namespace declarations', $stmt->getLine());
                }

                // for semicolon style namespaces remember the offset
                if (!$bracketed) {
                    $nsOffsets[] = $i;
                }
            // declare() and __halt_compiler() are the only valid statements outside of namespace declarations
            } elseif (!$stmt instanceof Declare_
                      && !$stmt instanceof HaltCompiler
            ) {
                if (true === $bracketed) {
                    throw new Error('No code may exist outside of namespace {}', $stmt->getLine());
                }

                $hasNotAllowedStmts = true;
            }
        }

        // if bracketed namespaces were used or no namespaces were used at all just return the
        // original statements
        if (!isset($bracketed) || true === $bracketed) {
            return $stmts;
        // for semicolon style transplant statements
        } else {
            // take all statements preceding the first namespace
            $newStmts = array_slice($stmts, 0, $nsOffsets[0]);

            // iterate over all following namespaces
            for ($i = 0, $c = count($nsOffsets); $i < $c; ++$i) {
                $newStmts[] = $nsStmt = $stmts[$nsOffsets[$i]];

                // the last namespace takes all statements after it
                if ($c === $i + 1) {
                    $nsStmt->stmts = array_slice($stmts, $nsOffsets[$i] + 1);

                    // if the last statement is __halt_compiler() put it outside the namespace
                    if (end($nsStmt->stmts) instanceof HaltCompiler) {
                        $newStmts[] = array_pop($nsStmt->stmts);
                    }
                // and all the others take all statements between the current and the following one
                } else {
                    $nsStmt->stmts = array_slice($stmts, $nsOffsets[$i] + 1, $nsOffsets[$i + 1] - $nsOffsets[$i] - 1);
                }
            }

            return $newStmts;
        }
    }
}