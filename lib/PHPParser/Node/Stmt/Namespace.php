<?php

/**
 * @property null|PHPParser_Node_Name $name  Name
 * @property array                    $stmts Statements
 */
class PHPParser_Node_Stmt_Namespace extends PHPParser_Node_Stmt
{
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, $line, $docComment);

        if ('self' === $this->name || 'parent' === $this->name) {
            throw new PHPParser_Error(sprintf('Cannot use "%s" as namespace name', $this->name), $line);
        }

        if (null !== $this->stmts) {
            foreach ($this->stmts as $stmt) {
                if ($stmt instanceof PHPParser_Node_Stmt_Namespace) {
                    throw new PHPParser_Error('Namespace declarations cannot be nested', $stmt->getLine());
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
            if ($stmt instanceof PHPParser_Node_Stmt_Namespace) {
                // ->stmts is null if semicolon style is used
                $currentBracketed = null !== $stmt->stmts;

                // if no namespace statement has been encountered yet
                if (!isset($bracketed)) {
                    // set the namespacing style
                    $bracketed = $currentBracketed;

                    // and ensure that it isn't preceded by a not allowed statement
                    if ($hasNotAllowedStmts) {
                        throw new PHPParser_Error('Namespace declaration statement has to be the very first statement in the script', $stmt->getLine());
                    }
                // otherwise ensure that the style of the current namespace matches the style of
                // namespaceing used before in this document
                } elseif ($bracketed !== $currentBracketed) {
                    throw new PHPParser_Error('Cannot mix bracketed namespace declarations with unbracketed namespace declarations', $stmt->getLine());
                }

                // for semicolon style namespaces remember the offset
                if (!$bracketed) {
                    $nsOffsets[] = $i;
                }
            // declare() is the only valid statement before a namespace
            } elseif (!$stmt instanceof PHPParser_Node_Stmt_Declare) {
                if (true === $bracketed) {
                    throw new PHPParser_Error('No code may exist outside of namespace {}', $stmt->getLine());
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
                $nsStmt = $stmts[$nsOffsets[$i]];

                // the last namespace takes all statements after it
                if ($c === $i + 1) {
                    $nsStmt->stmts = array_slice($stmts, $nsOffsets[$i] + 1);
                // and all the others take all statements between the current and the following one
                } else {
                    $nsStmt->stmts = array_slice($stmts, $nsOffsets[$i] + 1, $nsOffsets[$i + 1] - $nsOffsets[$i] - 1);
                }

                $newStmts[] = $nsStmt;
            }

            return $newStmts;
        }
    }
}