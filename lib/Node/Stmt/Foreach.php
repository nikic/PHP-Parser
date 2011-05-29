<?php

/**
 * @property Node_Expr          $expr     Expression to iterate
 * @property null|Node_Variable $keyVar   Variable to assign key to
 * @property bool               $byRef    Whether to assign value by reference
 * @property Node_Variable      $valueVar Variable to assign value to
 * @property array              $stmts    Statements
 */
class Node_Stmt_Foreach extends Node_Stmt
{
}