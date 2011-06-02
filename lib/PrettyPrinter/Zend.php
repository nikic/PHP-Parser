<?php

class PrettyPrinter_Zend extends PrettyPrinterAbstract
{
    // Names and Variables

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

    // Magic Constants

    public function pScalar_ClassConst(Node_Scalar_ClassConst $node) {
        return '__CLASS__';
    }

    public function pScalar_DirConst(Node_Scalar_DirConst $node) {
        return '__DIR__';
    }

    public function pScalar_FileConst(Node_Scalar_FileConst $node) {
        return '__FILE__';
    }

    public function pScalar_FuncConst(Node_Scalar_FuncConst $node) {
        return '__FUNCTION__';
    }

    public function pScalar_LineConst(Node_Scalar_LineConst $node) {
        return '__LINE__';
    }

    public function pScalar_MethodConst(Node_Scalar_MethodConst $node) {
        return '__METHOD__';
    }

    public function pScalar_NSConst(Node_Scalar_NSConst $node) {
        return '__NAMESPACE__';
    }

    // Scalars

    public function pScalar_String(Node_Scalar_String $node) {
        return ($node->isBinary ? 'b' : '')
             . (Node_Scalar_String::SINGLE_QUOTED === $node->type
                ? '\'' . $this->pSafe(addcslashes($node->value, '\'\\')) . '\''
                : '"' . addcslashes($node->value, "\n\r\t\f\v$\"\\") . '"'
        );
    }

    public function pScalar_Encapsed(Node_Scalar_Encapsed $node) {
        return '"' . $this->pEncapsList($node->parts) . '"';
    }

    public function pScalar_LNumber(Node_Scalar_LNumber $node) {
        return (string) $node->value;
    }

    public function pScalar_DNumber(Node_Scalar_DNumber $node) {
        return (string) $node->value;
    }

    // Assignments

    public function pExpr_Assign(Node_Expr_Assign $node) {
        return $this->p($node->var) . ' = ' . $this->p($node->expr);
    }

    public function pExpr_AssignRef(Node_Expr_AssignRef $node) {
        return $this->p($node->var) . ' =& ' . $this->p($node->refVar);
    }

    public function pExpr_AssignPlus(Node_Expr_AssignPlus $node) {
        return $this->p($node->var) . ' += ' . $this->p($node->expr);
    }

    public function pExpr_AssignMinus(Node_Expr_AssignMinus $node) {
        return $this->p($node->var) . ' -= ' . $this->p($node->expr);
    }

    public function pExpr_AssignMul(Node_Expr_AssignMul $node) {
        return $this->p($node->var) . ' *= ' . $this->p($node->expr);
    }

    public function pExpr_AssignDiv(Node_Expr_AssignDiv $node) {
        return $this->p($node->var) . ' /= ' . $this->p($node->expr);
    }

    public function pExpr_AssignConcat(Node_Expr_AssignConcat $node) {
        return $this->p($node->var) . ' .= ' . $this->p($node->expr);
    }

    public function pExpr_AssignMod(Node_Expr_AssignMod $node) {
        return $this->p($node->var) . ' %= ' . $this->p($node->expr);
    }

    public function pExpr_AssignBinAnd(Node_Expr_AssignBinAnd $node) {
        return $this->p($node->var) . ' &= ' . $this->p($node->expr);
    }

    public function pExpr_AssignBinOr(Node_Expr_AssignBinOr $node) {
        return $this->p($node->var) . ' |= ' . $this->p($node->expr);
    }

    public function pExpr_AssignBinXor(Node_Expr_AssignBinXor $node) {
        return $this->p($node->var) . ' ^= ' . $this->p($node->expr);
    }

    public function pExpr_AssignShiftLeft(Node_Expr_AssignShiftLeft $node) {
        return $this->p($node->var) . ' <<= ' . $this->p($node->expr);
    }

    public function pExpr_AssignShiftRight(Node_Expr_AssignShiftRight $node) {
        return $this->p($node->var) . ' >>= ' . $this->p($node->expr);
    }

    public function pExpr_List(Node_Expr_List $node) {
        $pAssignList = array();
        foreach ($node->assignList as $element) {
            if (null === $element) {
                $pAssignList[] = '';
            } else {
                $pAssignList[] = $this->p($element);
            }
        }

        return 'list(' . implode(', ', $pAssignList) . ') = ' . $this->p($node->expr);
    }

    // Binary expressions

    public function pExpr_Plus(Node_Expr_Plus $node) {
        return $this->p($node->left) . ' + ' . $this->p($node->right);
    }

    public function pExpr_Minus(Node_Expr_Minus $node) {
        return $this->p($node->left) . ' - ' . $this->p($node->right);
    }

    public function pExpr_Mul(Node_Expr_Mul $node) {
        return $this->p($node->left) . ' * ' . $this->p($node->right);
    }

    public function pExpr_Div(Node_Expr_Div $node) {
        return $this->p($node->left) . ' / ' . $this->p($node->right);
    }

    public function pExpr_Concat(Node_Expr_Concat $node) {
        return $this->p($node->left) . ' . ' . $this->p($node->right);
    }

    public function pExpr_Mod(Node_Expr_Mod $node) {
        return $this->p($node->left) . ' % ' . $this->p($node->right);
    }

    public function pExpr_BooleanAnd(Node_Expr_BooleanAnd $node) {
        return $this->p($node->left) . ' && ' . $this->p($node->right);
    }

    public function pExpr_BooleanOr(Node_Expr_BooleanOr $node) {
        return $this->p($node->left) . ' || ' . $this->p($node->right);
    }

    public function pExpr_BinaryAnd(Node_Expr_BinaryAnd $node) {
        return $this->p($node->left) . ' & ' . $this->p($node->right);
    }

    public function pExpr_BinaryOr(Node_Expr_BinaryOr $node) {
        return $this->p($node->left) . ' | ' . $this->p($node->right);
    }

    public function pExpr_BinaryXor(Node_Expr_BinaryXor $node) {
        return $this->p($node->left) . ' ^ ' . $this->p($node->right);
    }

    public function pExpr_ShiftLeft(Node_Expr_ShiftLeft $node) {
        return $this->p($node->left) . ' << ' . $this->p($node->right);
    }

    public function pExpr_ShiftRight(Node_Expr_ShiftRight $node) {
        return $this->p($node->left) . ' >> ' . $this->p($node->right);
    }

    public function pExpr_LogicalAnd(Node_Expr_LogicalAnd $node) {
        return $this->p($node->left) . ' and ' . $this->p($node->right);
    }

    public function pExpr_LogicalOr(Node_Expr_LogicalOr $node) {
        return $this->p($node->left) . ' or ' . $this->p($node->right);
    }

    public function pExpr_LogicalXor(Node_Expr_LogicalXor $node) {
        return $this->p($node->left) . ' xor ' . $this->p($node->right);
    }

    public function pExpr_Equal(Node_Expr_Equal $node) {
        return $this->p($node->left) . ' == ' . $this->p($node->right);
    }

    public function pExpr_NotEqual(Node_Expr_NotEqual $node) {
        return $this->p($node->left) . ' != ' . $this->p($node->right);
    }

    public function pExpr_Identical(Node_Expr_Identical $node) {
        return $this->p($node->left) . ' === ' . $this->p($node->right);
    }

    public function pExpr_NotIdentical(Node_Expr_NotIdentical $node) {
        return $this->p($node->left) . ' !== ' . $this->p($node->right);
    }

    public function pExpr_Greater(Node_Expr_Greater $node) {
        return $this->p($node->left) . ' > ' . $this->p($node->right);
    }

    public function pExpr_GreaterOrEqual(Node_Expr_GreaterOrEqual $node) {
        return $this->p($node->left) . ' >= ' . $this->p($node->right);
    }

    public function pExpr_Smaller(Node_Expr_Smaller $node) {
        return $this->p($node->left) . ' < ' . $this->p($node->right);
    }

    public function pExpr_SmallerOrEqual(Node_Expr_SmallerOrEqual $node) {
        return $this->p($node->left) . ' <= ' . $this->p($node->right);
    }

    public function pExpr_Instanceof(Node_Expr_Instanceof $node) {
        return $this->p($node->expr) . ' instanceof ' . $this->p($node->class);
    }

    // Unary expressions

    public function pExpr_BooleanNot(Node_Expr_BooleanNot $node) {
        return '!' . $this->p($node->expr);
    }

    public function pExpr_BinaryNot(Node_Expr_BinaryNot $node) {
        return '~' . $this->p($node->expr);
    }

    public function pExpr_UnaryMinus(Node_Expr_UnaryMinus $node) {
        return '-' . $this->p($node->expr);
    }

    public function pExpr_UnaryPlus(Node_Expr_UnaryPlus $node) {
        return '+' . $this->p($node->expr);
    }

    public function pExpr_PreInc(Node_Expr_PreInc $node) {
        return '++' . $this->p($node->var);
    }

    public function pExpr_PreDec(Node_Expr_PreDec $node) {
        return '--' . $this->p($node->var);
    }

    public function pExpr_PostInc(Node_Expr_PostInc $node) {
        return $this->p($node->var) . '++';
    }

    public function pExpr_PostDec(Node_Expr_PostDec $node) {
        return $this->p($node->var) . '--';
    }

    public function pExpr_ErrorSupress(Node_Expr_ErrorSupress $node) {
        return '@' . $this->p($node->expr);
    }

    // Casts

    public function pExpr_IntCast(Node_Expr_IntCast $node) {
        return '(int) ' . $this->p($node->expr);
    }

    public function pExpr_DoubleCast(Node_Expr_DoubleCast $node) {
        return '(double) ' . $this->p($node->expr);
    }

    public function pExpr_StringCast(Node_Expr_StringCast $node) {
        return '(string) ' . $this->p($node->expr);
    }

    public function pExpr_ArrayCast(Node_Expr_ArrayCast $node) {
        return '(array) ' . $this->p($node->expr);
    }

    public function pExpr_ObjectCast(Node_Expr_ObjectCast $node) {
        return '(object) ' . $this->p($node->expr);
    }

    public function pExpr_BoolCast(Node_Expr_BoolCast $node) {
        return '(bool) ' . $this->p($node->expr);
    }

    public function pExpr_UnsetCast(Node_Expr_UnsetCast $node) {
        return '(unset) ' . $this->p($node->expr);
    }

    // Function calls and similar constructs

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

    public function pExpr_StaticCall(Node_Expr_StaticCall $node) {
        return $this->pClassName($node->class) . '::'
             . ($node->func instanceof Node_Expr ? $this->p($node->func) : $node->func)
             . '(' . $this->pCommaSeparated($node->args) . ')';
    }

    public function pExpr_Empty(Node_Expr_Empty $node) {
        return 'empty(' . $this->p($node->var) . ')';
    }

    public function pExpr_Isset(Node_Expr_Isset $node) {
        return 'isset(' . $this->pCommaSeparated($node->vars) . ')';
    }

    public function pExpr_Print(Node_Expr_Print $node) {
        return 'print ' . $this->p($node->expr);
    }

    public function pExpr_Eval(Node_Expr_Eval $node) {
        return 'eval(' . $this->p($node->expr) . ')';
    }

    public function pExpr_Include(Node_Expr_Include $node) {
        static $map = array(
            Node_Expr_Include::TYPE_INCLUDE      => 'include',
            Node_Expr_Include::TYPE_INCLUDE_ONCE => 'include_once',
            Node_Expr_Include::TYPE_REQUIRE      => 'require',
            Node_Expr_Include::TYPE_REQUIRE_ONCE => 'require_once',
        );

        return $map[$node->type] . ' ' . $this->p($node->expr);
    }

    // Other

    public function pExpr_Array(Node_Expr_Array $node) {
        return 'array(' . $this->pCommaSeparated($node->items) . ')';
    }

    public function pExpr_ArrayItem(Node_Expr_ArrayItem $node) {
        return (null !== $node->key ? $this->p($node->key) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->value);
    }

    public function pExpr_ArrayDimFetch(Node_Expr_ArrayDimFetch $node) {
        return $this->p($node->var) . '[' . (null !== $node->dim ? $this->p($node->dim) : '') . ']';
    }

    public function pExpr_ConstFetch(Node_Expr_ConstFetch $node) {
        return $this->p($node->name);
    }

    public function pExpr_ClassConstFetch(Node_Expr_ClassConstFetch $node) {
        return $this->pClassName($node->class) . '::' . $node->name;
    }

    public function pExpr_PropertyFetch(Node_Expr_PropertyFetch $node) {
        return $this->p($node->var) . '->' . $this->pObjectProperty($node->name);
    }

    public function pExpr_StaticPropertyFetch(Node_Expr_StaticPropertyFetch $node) {
        return $this->pClassName($node->class) . '::$' . $this->pObjectProperty($node->name);
    }

    public function pExpr_ShellExec(Node_Expr_ShellExec $node) {
        return '`' . $this->pEncapsList($node->parts) . '`';
    }

    public function pExpr_LambdaFunc(Node_Expr_LambdaFunc $node) {
        return 'function ' . ($node->byRef ? '&' : '')
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (!empty($node->useVars) ? ' use(' . $this->pCommaSeparated($node->useVars) . ')': '')
             . ' {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pExpr_LambdaFuncUse(Node_Expr_LambdaFuncUse $node) {
        return ($node->byRef ? '&' : '') . '$' . $node->var;
    }

    public function pExpr_New(Node_Expr_New $node) {
        return 'new ' . $this->pClassName($node->class) . '(' . $this->pCommaSeparated($node->args) . ')';
    }

    public function pExpr_Clone(Node_Expr_Clone $node) {
        return 'clone ' . $this->p($node->expr);
    }

    public function pExpr_Ternary(Node_Expr_Ternary $node) {
        return $this->p($node->cond) . ' ?'
             . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '')
             . ': ' . $this->p($node->else);
    }

    public function pExpr_Exit(Node_Expr_Exit $node) {
        return 'die' . (null !== $node->expr ? '(' . $this->p($node->expr) . ')' : '');
    }

    // Declarations

    public function pStmt_Namespace(Node_Stmt_Namespace $node) {
        return 'namespace ' . $this->p($node->ns);
    }

    public function pStmt_Use(Node_Stmt_Use $node) {
        return 'use ' . $this->pCommaSeparated($node->uses);
    }

    public function pStmt_UseUse(Node_Stmt_UseUse $node) {
        return $this->p($node->ns) . (null !== $node->alias ? ' as ' . $node->alias : '');
    }

    public function pStmt_Interface(Node_Stmt_Interface $node) {
        return 'interface ' . $node->name
             . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '')
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Class(Node_Stmt_Class $node) {
        return $this->pModifiers($node->type)
             . 'class ' . $node->name
             . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
             . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Property(Node_Stmt_Property $node) {
        return $this->pModifiers($node->type) . $this->pCommaSeparated($node->props);
    }

    public function pStmt_PropertyProperty(Node_Stmt_PropertyProperty $node) {
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }

    public function pStmt_ClassMethod(Node_Stmt_ClassMethod $node) {
        return $this->pModifiers($node->type)
             . 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (null !== $node->stmts
                ? "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}'
                : ';');
    }

    public function pStmt_ClassConst(Node_Stmt_ClassConst $node) {
        return 'const ' . $this->pCommaSeparated($node->consts);
    }

    public function pStmt_ClassConstConst(Node_Stmt_ClassConstConst $node) {
        return $node->name . ' = ' . $this->p($node->value);
    }

    public function pStmt_Func(Node_Stmt_Func $node) {
        return 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_FuncParam(Node_Stmt_FuncParam $node) {
        return ($node->type ? ('array' == $node->type ? 'array' : $this->p($node->type)) . ' ' : '')
             . ($node->byRef ? '&' : '')
             . '$' . $node->name
             . ($node->default ? ' = ' . $this->p($node->default) : '');
    }

    public function pStmt_Const(Node_Stmt_Const $node) {
        return 'const ' . $this->pCommaSeparated($node->consts);
    }

    public function pStmt_ConstConst(Node_Stmt_ConstConst $node) {
        return $node->name . ' = ' . $this->p($node->value);
    }

    // Control flow

    public function pStmt_If(Node_Stmt_If $node) {
        return 'if (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}'
             . $this->pImplode($node->elseifList)
             . (null !== $node->else ? $this->p($node->else) : '');
    }

    public function pStmt_Elseif(Node_Stmt_Elseif $node) {
        return ' elseif (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Else(Node_Stmt_Else $node) {
        return ' else {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_For(Node_Stmt_For $node) {
        return 'for ('
             . $this->pCommaSeparated($node->init) . ';'
             . $this->pCommaSeparated($node->cond) . ';'
             . $this->pCommaSeparated($node->loop)
             . ') {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Foreach(Node_Stmt_Foreach $node) {
        return 'foreach (' . $this->p($node->expr) . ' as '
             . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_While(Node_Stmt_While $node) {
        return 'while (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Do(Node_Stmt_Do $node) {
        return 'do {' . "\n" . $this->pStmts($node->stmts)
             . '} while (' . $this->p($node->cond) . "\n" . ')';
    }

    public function pStmt_Switch(Node_Stmt_Switch $node) {
        return 'switch (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pImplode($node->caseList) . '}';
    }

    public function pStmt_TryCatch(Node_Stmt_TryCatch $node) {
        return 'try {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}'
             . $this->pImplode($node->catches);
    }

    public function pStmt_Catch(Node_Stmt_Catch $node) {
        return ' catch (' . $this->p($node->type) . ' $' . $node->var . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Case(Node_Stmt_Case $node) {
        return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':'
             . "\n" . $this->pStmts($node->stmts) . "\n";
    }

    public function pStmt_Break(Node_Stmt_Break $node) {
        return 'break' . ($node->num !== null ? ' ' . $this->p($node->num) : '');
    }

    public function pStmt_Continue(Node_Stmt_Continue $node) {
        return 'continue' . ($node->num !== null ? ' ' . $this->p($node->num) : '');
    }

    public function pStmt_Return(Node_Stmt_Return $node) {
        return 'return' . (null !== $node->expr ? ' ' . $this->p($node->expr) : '');
    }

    public function pStmt_Throw(Node_Stmt_Throw $node) {
        return 'throw' . (null !== $node->expr ? ' ' . $this->p($node->expr) : '');
    }

    public function pStmt_Label(Node_Stmt_Label $node) {
        return $node->name . ':';
    }

    public function pStmt_Goto(Node_Stmt_Goto $node) {
        return 'goto ' . $node->name;
    }

    // Other

    public function pStmt_Echo(Node_Stmt_Echo $node) {
        return 'echo ' . $this->pCommaSeparated($node->exprs);
    }

    public function pStmt_Static(Node_Stmt_Static $node) {
        return 'static ' . $this->pCommaSeparated($node->vars);
    }

    public function pStmt_Global(Node_Stmt_Global $node) {
        return 'global ' . $this->pCommaSeparated($node->vars);
    }

    public function pStmt_StaticVar(Node_Stmt_StaticVar $node) {
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }

    public function pStmt_Unset(Node_Stmt_Unset $node) {
        return 'unset(' . $this->pCommaSeparated($node->vars) . ')';
    }

    public function pStmt_InlineHTML(Node_Stmt_InlineHTML $node) {
        return '?>' . $this->pSafe(
            ("\n" === $node->value[0] || "\r" === $node->value[0] ? "\n" : '')
            . $node->value
        ) . '<?php ';
    }

    // Helpers

    public function pObjectProperty($node) {
        if ($node instanceof Node_Variable || $node instanceof Node_Expr_ArrayDimFetch) {
            return $this->p($node);
        } elseif ($node instanceof Node_Expr) {
            return '{' . $this->p($node) . '}';
        } else {
            return $node;
        }
    }

    public function pClassName($node) {
        if ($node == 'static') {
            return 'static';
        } else {
            return $this->p($node);
        }
    }

    public function pModifiers($modifiers) {
        return ($modifiers & Node_Stmt_Class::MODIFIER_PUBLIC    ? 'public '    : '')
             . ($modifiers & Node_Stmt_Class::MODIFIER_PROTECTED ? 'protected ' : '')
             . ($modifiers & Node_Stmt_Class::MODIFIER_PRIVATE   ? 'private '   : '')
             . ($modifiers & Node_Stmt_Class::MODIFIER_STATIC    ? 'static '    : '')
             . ($modifiers & Node_Stmt_Class::MODIFIER_ABSTRACT  ? 'abstract '  : '')
             . ($modifiers & Node_Stmt_Class::MODIFIER_FINAL     ? 'final '     : '');
    }

    public function pEncapsList(array $encapsList) {
        $return = '';
        foreach ($encapsList as $i => $element) {
            if (is_string($element)) {
                $return .= addcslashes($element, "\n\r\t\f\v$\"\\");
            } else {
                $return .= '{' . $this->p($element) . '}';
            }
        }

        return $return;
    }
}