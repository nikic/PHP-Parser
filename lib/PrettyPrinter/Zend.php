<?php

class PrettyPrinter_Zend extends PrettyPrinterAbstract
{
    public function pName(Node_Name $node) {
        return implode('\\', $node->parts);
    }

    public function pVariable(Node_Variable $node) {
        if ($node->name instanceof Node_Expr) {
            return '${' . $this->p($node->name) . '}';
        } elseif ($node->name instanceof Node_Variable) {
            return '$' . $this->p($node->name);
        } else {
            return '$' . $node->name;
        }
    }

    public function pScalar_FileConst(Node_Scalar_FileConst $node) {
        return '__FILE__';
    }

    public function pScalar_String(Node_Scalar_String $node) {
        return ($node->isBinary ? 'b' : '')
             . (Node_Scalar_String::SINGLE_QUOTED === $node->type
                ? '\'' . addcslashes($node->value, '\'\\') . '\''
                : '"' . addcslashes($node->value, "\n\r\t\f\v$\"\\") . '"'
        );
    }

    public function pExpr_Assign(Node_Expr_Assign $node) {
        return $this->p($node->var) . ' = ' . $this->p($node->expr);
    }

    public function pExpr_BooleanAnd(Node_Expr_BooleanAnd $node) {
        return $this->p($node->left) . ' && ' . $this->p($node->right);
    }

    public function pExpr_Concat(Node_Expr_Concat $node) {
        return $this->p($node->left) . ' . ' . $this->p($node->right);
    }

    public function pExpr_ConstFetch(Node_Expr_ConstFetch $node) {
        return $this->p($node->name);
    }

    public function pExpr_FuncCall(Node_Expr_FuncCall $node) {
        return $this->p($node->func) . '(' . $this->pCommaSeparated($node->args) . ')';
    }

    public function pExpr_FuncCallArg(Node_Expr_FuncCallArg $node) {
        return ($node->byRef ? '&' : '') . $this->p($node->value);
    }

    public function pExpr_MethodCall(Node_Expr_MethodCall $node) {
        return $this->p($node->var) . '->' . $this->pObjectProperty($node->name)
             . '(' . $this->pCommaSeparated($node->args) . ')';
    }

    public function pExpr_Minus(Node_Expr_Minus $node) {
        return $this->p($node->left) . ' - ' . $this->p($node->right);
    }

    public function pExpr_New(Node_Expr_New $node) {
        return 'new ' . $this->p($node->class) . '(' . $this->pCommaSeparated($node->args) . ')';
    }

    public function pExpr_PropertyFetch(Node_Expr_PropertyFetch $node) {
        return $this->p($node->var) . '->' . $this->pObjectProperty($node->name);
    }

    public function pStmt_Echo(Node_Stmt_Echo $node) {
        return 'echo ' . $this->pCommaSeparated($node->exprs);
    }

    public function pStmt_Func(Node_Stmt_Func $node) {
        return 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')' . "\n" . '{' . "\n"
             . $this->pIndent($this->pStmts($node->stmts)) . '}';
    }

    public function pStmt_FuncParam(Node_Stmt_FuncParam $node) {
        return ($node->type ? ('array' == $node->type ? 'array' : $this->p($node->type)) . ' ' : '')
             . ($node->byRef ? '&' : '')
             . '$' . $node->name
             . ($node->default ? ' = ' . $this->p($node->default) : '');
    }

    // helpers

    public function pObjectProperty($node) {
        if ($node instanceof Node_Expr) {
            return '{' . $this->p($node) . '}';
        } elseif ($node instanceof Node_Variable) {
            return $this->pVariable($node);
        } else {
            return $node;
        }
    }
}