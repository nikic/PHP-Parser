<?php

namespace PhpParser\PrettyPrinter;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinterAbstract;

class Standard extends PrettyPrinterAbstract
{
    // Special nodes

    protected function pParam(Node\Param $node) {
        return ($node->type ? $this->pType($node->type) . ' ' : '')
             . ($node->byRef ? '&' : '')
             . ($node->variadic ? '...' : '')
             . '$' . $node->name
             . ($node->default ? ' = ' . $this->p($node->default) : '');
    }

    protected function pArg(Node\Arg $node) {
        return ($node->byRef ? '&' : '') . ($node->unpack ? '...' : '') . $this->p($node->value);
    }

    protected function pConst(Node\Const_ $node) {
        return $node->name . ' = ' . $this->p($node->value);
    }

    protected function pNullableType(Node\NullableType $node) {
        return '?' . $this->pType($node->type);
    }

    // Names

    protected function pName(Name $node) {
        return implode('\\', $node->parts);
    }

    protected function pName_FullyQualified(Name\FullyQualified $node) {
        return '\\' . implode('\\', $node->parts);
    }

    protected function pName_Relative(Name\Relative $node) {
        return 'namespace\\' . implode('\\', $node->parts);
    }

    // Magic Constants

    protected function pScalar_MagicConst_Class(MagicConst\Class_ $node) {
        return '__CLASS__';
    }

    protected function pScalar_MagicConst_Dir(MagicConst\Dir $node) {
        return '__DIR__';
    }

    protected function pScalar_MagicConst_File(MagicConst\File $node) {
        return '__FILE__';
    }

    protected function pScalar_MagicConst_Function(MagicConst\Function_ $node) {
        return '__FUNCTION__';
    }

    protected function pScalar_MagicConst_Line(MagicConst\Line $node) {
        return '__LINE__';
    }

    protected function pScalar_MagicConst_Method(MagicConst\Method $node) {
        return '__METHOD__';
    }

    protected function pScalar_MagicConst_Namespace(MagicConst\Namespace_ $node) {
        return '__NAMESPACE__';
    }

    protected function pScalar_MagicConst_Trait(MagicConst\Trait_ $node) {
        return '__TRAIT__';
    }

    // Scalars

    protected function pScalar_String(Scalar\String_ $node) {
        $kind = $node->getAttribute('kind', Scalar\String_::KIND_SINGLE_QUOTED);
        switch ($kind) {
            case Scalar\String_::KIND_NOWDOC:
                $label = $node->getAttribute('docLabel');
                if ($label && !$this->containsEndLabel($node->value, $label)) {
                    if ($node->value === '') {
                        return $this->pNoIndent("<<<'$label'\n$label") . $this->docStringEndToken;
                    }

                    return $this->pNoIndent("<<<'$label'\n$node->value\n$label")
                         . $this->docStringEndToken;
                }
                /* break missing intentionally */
            case Scalar\String_::KIND_SINGLE_QUOTED:
                return '\'' . $this->pNoIndent(addcslashes($node->value, '\'\\')) . '\'';
            case Scalar\String_::KIND_HEREDOC:
                $label = $node->getAttribute('docLabel');
                if ($label && !$this->containsEndLabel($node->value, $label)) {
                    if ($node->value === '') {
                        return $this->pNoIndent("<<<$label\n$label") . $this->docStringEndToken;
                    }

                    $escaped = $this->escapeString($node->value, null);
                    return $this->pNoIndent("<<<$label\n" . $escaped ."\n$label")
                         . $this->docStringEndToken;
                }
            /* break missing intentionally */
            case Scalar\String_::KIND_DOUBLE_QUOTED:
                return '"' . $this->escapeString($node->value, '"') . '"';
        }
        throw new \Exception('Invalid string kind');
    }

    protected function pScalar_Encapsed(Scalar\Encapsed $node) {
        if ($node->getAttribute('kind') === Scalar\String_::KIND_HEREDOC) {
            $label = $node->getAttribute('docLabel');
            if ($label && !$this->encapsedContainsEndLabel($node->parts, $label)) {
                if (count($node->parts) === 1
                    && $node->parts[0] instanceof Scalar\EncapsedStringPart
                    && $node->parts[0]->value === ''
                ) {
                    return $this->pNoIndent("<<<$label\n$label") . $this->docStringEndToken;
                }

                return $this->pNoIndent(
                    "<<<$label\n" . $this->pEncapsList($node->parts, null) . "\n$label"
                ) . $this->docStringEndToken;
            }
        }
        return '"' . $this->pEncapsList($node->parts, '"') . '"';
    }

    protected function pScalar_LNumber(Scalar\LNumber $node) {
        if ($node->value === -\PHP_INT_MAX-1) {
            // PHP_INT_MIN cannot be represented as a literal,
            // because the sign is not part of the literal
            return '(-' . \PHP_INT_MAX . '-1)';
        }

        $kind = $node->getAttribute('kind', Scalar\LNumber::KIND_DEC);
        if (Scalar\LNumber::KIND_DEC === $kind) {
            return (string) $node->value;
        }

        $sign = $node->value < 0 ? '-' : '';
        $str = (string) $node->value;
        switch ($kind) {
            case Scalar\LNumber::KIND_BIN:
                return $sign . '0b' . base_convert($str, 10, 2);
            case Scalar\LNumber::KIND_OCT:
                return $sign . '0' . base_convert($str, 10, 8);
            case Scalar\LNumber::KIND_HEX:
                return $sign . '0x' . base_convert($str, 10, 16);
        }
        throw new \Exception('Invalid number kind');
    }

    protected function pScalar_DNumber(Scalar\DNumber $node) {
        if (!is_finite($node->value)) {
            if ($node->value === \INF) {
                return '\INF';
            } elseif ($node->value === -\INF) {
                return '-\INF';
            } else {
                return '\NAN';
            }
        }

        // Try to find a short full-precision representation
        $stringValue = sprintf('%.16G', $node->value);
        if ($node->value !== (double) $stringValue) {
            $stringValue = sprintf('%.17G', $node->value);
        }

        // %G is locale dependent and there exists no locale-independent alternative. We don't want
        // mess with switching locales here, so let's assume that a comma is the only non-standard
        // decimal separator we may encounter...
        $stringValue = str_replace(',', '.', $stringValue);

        // ensure that number is really printed as float
        return preg_match('/^-?[0-9]+$/', $stringValue) ? $stringValue . '.0' : $stringValue;
    }

    // Assignments

    protected function pExpr_Assign(Expr\Assign $node) {
        return $this->pInfixOp('Expr_Assign', $node->var, ' = ', $node->expr);
    }

    protected function pExpr_AssignRef(Expr\AssignRef $node) {
        return $this->pInfixOp('Expr_AssignRef', $node->var, ' =& ', $node->expr);
    }

    protected function pExpr_AssignOp_Plus(AssignOp\Plus $node) {
        return $this->pInfixOp('Expr_AssignOp_Plus', $node->var, ' += ', $node->expr);
    }

    protected function pExpr_AssignOp_Minus(AssignOp\Minus $node) {
        return $this->pInfixOp('Expr_AssignOp_Minus', $node->var, ' -= ', $node->expr);
    }

    protected function pExpr_AssignOp_Mul(AssignOp\Mul $node) {
        return $this->pInfixOp('Expr_AssignOp_Mul', $node->var, ' *= ', $node->expr);
    }

    protected function pExpr_AssignOp_Div(AssignOp\Div $node) {
        return $this->pInfixOp('Expr_AssignOp_Div', $node->var, ' /= ', $node->expr);
    }

    protected function pExpr_AssignOp_Concat(AssignOp\Concat $node) {
        return $this->pInfixOp('Expr_AssignOp_Concat', $node->var, ' .= ', $node->expr);
    }

    protected function pExpr_AssignOp_Mod(AssignOp\Mod $node) {
        return $this->pInfixOp('Expr_AssignOp_Mod', $node->var, ' %= ', $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseAnd(AssignOp\BitwiseAnd $node) {
        return $this->pInfixOp('Expr_AssignOp_BitwiseAnd', $node->var, ' &= ', $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseOr(AssignOp\BitwiseOr $node) {
        return $this->pInfixOp('Expr_AssignOp_BitwiseOr', $node->var, ' |= ', $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseXor(AssignOp\BitwiseXor $node) {
        return $this->pInfixOp('Expr_AssignOp_BitwiseXor', $node->var, ' ^= ', $node->expr);
    }

    protected function pExpr_AssignOp_ShiftLeft(AssignOp\ShiftLeft $node) {
        return $this->pInfixOp('Expr_AssignOp_ShiftLeft', $node->var, ' <<= ', $node->expr);
    }

    protected function pExpr_AssignOp_ShiftRight(AssignOp\ShiftRight $node) {
        return $this->pInfixOp('Expr_AssignOp_ShiftRight', $node->var, ' >>= ', $node->expr);
    }

    protected function pExpr_AssignOp_Pow(AssignOp\Pow $node) {
        return $this->pInfixOp('Expr_AssignOp_Pow', $node->var, ' **= ', $node->expr);
    }

    // Binary expressions

    protected function pExpr_BinaryOp_Plus(BinaryOp\Plus $node) {
        return $this->pInfixOp('Expr_BinaryOp_Plus', $node->left, ' + ', $node->right);
    }

    protected function pExpr_BinaryOp_Minus(BinaryOp\Minus $node) {
        return $this->pInfixOp('Expr_BinaryOp_Minus', $node->left, ' - ', $node->right);
    }

    protected function pExpr_BinaryOp_Mul(BinaryOp\Mul $node) {
        return $this->pInfixOp('Expr_BinaryOp_Mul', $node->left, ' * ', $node->right);
    }

    protected function pExpr_BinaryOp_Div(BinaryOp\Div $node) {
        return $this->pInfixOp('Expr_BinaryOp_Div', $node->left, ' / ', $node->right);
    }

    protected function pExpr_BinaryOp_Concat(BinaryOp\Concat $node) {
        return $this->pInfixOp('Expr_BinaryOp_Concat', $node->left, ' . ', $node->right);
    }

    protected function pExpr_BinaryOp_Mod(BinaryOp\Mod $node) {
        return $this->pInfixOp('Expr_BinaryOp_Mod', $node->left, ' % ', $node->right);
    }

    protected function pExpr_BinaryOp_BooleanAnd(BinaryOp\BooleanAnd $node) {
        return $this->pInfixOp('Expr_BinaryOp_BooleanAnd', $node->left, ' && ', $node->right);
    }

    protected function pExpr_BinaryOp_BooleanOr(BinaryOp\BooleanOr $node) {
        return $this->pInfixOp('Expr_BinaryOp_BooleanOr', $node->left, ' || ', $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseAnd(BinaryOp\BitwiseAnd $node) {
        return $this->pInfixOp('Expr_BinaryOp_BitwiseAnd', $node->left, ' & ', $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseOr(BinaryOp\BitwiseOr $node) {
        return $this->pInfixOp('Expr_BinaryOp_BitwiseOr', $node->left, ' | ', $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseXor(BinaryOp\BitwiseXor $node) {
        return $this->pInfixOp('Expr_BinaryOp_BitwiseXor', $node->left, ' ^ ', $node->right);
    }

    protected function pExpr_BinaryOp_ShiftLeft(BinaryOp\ShiftLeft $node) {
        return $this->pInfixOp('Expr_BinaryOp_ShiftLeft', $node->left, ' << ', $node->right);
    }

    protected function pExpr_BinaryOp_ShiftRight(BinaryOp\ShiftRight $node) {
        return $this->pInfixOp('Expr_BinaryOp_ShiftRight', $node->left, ' >> ', $node->right);
    }

    protected function pExpr_BinaryOp_Pow(BinaryOp\Pow $node) {
        return $this->pInfixOp('Expr_BinaryOp_Pow', $node->left, ' ** ', $node->right);
    }

    protected function pExpr_BinaryOp_LogicalAnd(BinaryOp\LogicalAnd $node) {
        return $this->pInfixOp('Expr_BinaryOp_LogicalAnd', $node->left, ' and ', $node->right);
    }

    protected function pExpr_BinaryOp_LogicalOr(BinaryOp\LogicalOr $node) {
        return $this->pInfixOp('Expr_BinaryOp_LogicalOr', $node->left, ' or ', $node->right);
    }

    protected function pExpr_BinaryOp_LogicalXor(BinaryOp\LogicalXor $node) {
        return $this->pInfixOp('Expr_BinaryOp_LogicalXor', $node->left, ' xor ', $node->right);
    }

    protected function pExpr_BinaryOp_Equal(BinaryOp\Equal $node) {
        return $this->pInfixOp('Expr_BinaryOp_Equal', $node->left, ' == ', $node->right);
    }

    protected function pExpr_BinaryOp_NotEqual(BinaryOp\NotEqual $node) {
        return $this->pInfixOp('Expr_BinaryOp_NotEqual', $node->left, ' != ', $node->right);
    }

    protected function pExpr_BinaryOp_Identical(BinaryOp\Identical $node) {
        return $this->pInfixOp('Expr_BinaryOp_Identical', $node->left, ' === ', $node->right);
    }

    protected function pExpr_BinaryOp_NotIdentical(BinaryOp\NotIdentical $node) {
        return $this->pInfixOp('Expr_BinaryOp_NotIdentical', $node->left, ' !== ', $node->right);
    }

    protected function pExpr_BinaryOp_Spaceship(BinaryOp\Spaceship $node) {
        return $this->pInfixOp('Expr_BinaryOp_Spaceship', $node->left, ' <=> ', $node->right);
    }

    protected function pExpr_BinaryOp_Greater(BinaryOp\Greater $node) {
        return $this->pInfixOp('Expr_BinaryOp_Greater', $node->left, ' > ', $node->right);
    }

    protected function pExpr_BinaryOp_GreaterOrEqual(BinaryOp\GreaterOrEqual $node) {
        return $this->pInfixOp('Expr_BinaryOp_GreaterOrEqual', $node->left, ' >= ', $node->right);
    }

    protected function pExpr_BinaryOp_Smaller(BinaryOp\Smaller $node) {
        return $this->pInfixOp('Expr_BinaryOp_Smaller', $node->left, ' < ', $node->right);
    }

    protected function pExpr_BinaryOp_SmallerOrEqual(BinaryOp\SmallerOrEqual $node) {
        return $this->pInfixOp('Expr_BinaryOp_SmallerOrEqual', $node->left, ' <= ', $node->right);
    }

    protected function pExpr_BinaryOp_Coalesce(BinaryOp\Coalesce $node) {
        return $this->pInfixOp('Expr_BinaryOp_Coalesce', $node->left, ' ?? ', $node->right);
    }

    protected function pExpr_Instanceof(Expr\Instanceof_ $node) {
        return $this->pInfixOp('Expr_Instanceof', $node->expr, ' instanceof ', $node->class);
    }

    // Unary expressions

    protected function pExpr_BooleanNot(Expr\BooleanNot $node) {
        return $this->pPrefixOp('Expr_BooleanNot', '!', $node->expr);
    }

    protected function pExpr_BitwiseNot(Expr\BitwiseNot $node) {
        return $this->pPrefixOp('Expr_BitwiseNot', '~', $node->expr);
    }

    protected function pExpr_UnaryMinus(Expr\UnaryMinus $node) {
        return $this->pPrefixOp('Expr_UnaryMinus', '-', $node->expr);
    }

    protected function pExpr_UnaryPlus(Expr\UnaryPlus $node) {
        return $this->pPrefixOp('Expr_UnaryPlus', '+', $node->expr);
    }

    protected function pExpr_PreInc(Expr\PreInc $node) {
        return $this->pPrefixOp('Expr_PreInc', '++', $node->var);
    }

    protected function pExpr_PreDec(Expr\PreDec $node) {
        return $this->pPrefixOp('Expr_PreDec', '--', $node->var);
    }

    protected function pExpr_PostInc(Expr\PostInc $node) {
        return $this->pPostfixOp('Expr_PostInc', $node->var, '++');
    }

    protected function pExpr_PostDec(Expr\PostDec $node) {
        return $this->pPostfixOp('Expr_PostDec', $node->var, '--');
    }

    protected function pExpr_ErrorSuppress(Expr\ErrorSuppress $node) {
        return $this->pPrefixOp('Expr_ErrorSuppress', '@', $node->expr);
    }

    protected function pExpr_YieldFrom(Expr\YieldFrom $node) {
        return $this->pPrefixOp('Expr_YieldFrom', 'yield from ', $node->expr);
    }

    protected function pExpr_Print(Expr\Print_ $node) {
        return $this->pPrefixOp('Expr_Print', 'print ', $node->expr);
    }

    // Casts

    protected function pExpr_Cast_Int(Cast\Int_ $node) {
        return $this->pPrefixOp('Expr_Cast_Int', '(int) ', $node->expr);
    }

    protected function pExpr_Cast_Double(Cast\Double $node) {
        return $this->pPrefixOp('Expr_Cast_Double', '(double) ', $node->expr);
    }

    protected function pExpr_Cast_String(Cast\String_ $node) {
        return $this->pPrefixOp('Expr_Cast_String', '(string) ', $node->expr);
    }

    protected function pExpr_Cast_Array(Cast\Array_ $node) {
        return $this->pPrefixOp('Expr_Cast_Array', '(array) ', $node->expr);
    }

    protected function pExpr_Cast_Object(Cast\Object_ $node) {
        return $this->pPrefixOp('Expr_Cast_Object', '(object) ', $node->expr);
    }

    protected function pExpr_Cast_Bool(Cast\Bool_ $node) {
        return $this->pPrefixOp('Expr_Cast_Bool', '(bool) ', $node->expr);
    }

    protected function pExpr_Cast_Unset(Cast\Unset_ $node) {
        return $this->pPrefixOp('Expr_Cast_Unset', '(unset) ', $node->expr);
    }

    // Function calls and similar constructs

    protected function pExpr_FuncCall(Expr\FuncCall $node) {
        return $this->pCallLhs($node->name)
             . '(' . $this->pMaybeMultiline($node->args) . ')';
    }

    protected function pExpr_MethodCall(Expr\MethodCall $node) {
        return $this->pDereferenceLhs($node->var) . '->' . $this->pObjectProperty($node->name)
             . '(' . $this->pMaybeMultiline($node->args) . ')';
    }

    protected function pExpr_StaticCall(Expr\StaticCall $node) {
        return $this->pDereferenceLhs($node->class) . '::'
             . ($node->name instanceof Expr
                ? ($node->name instanceof Expr\Variable
                   ? $this->p($node->name)
                   : '{' . $this->p($node->name) . '}')
                : $node->name)
             . '(' . $this->pMaybeMultiline($node->args) . ')';
    }

    protected function pExpr_Empty(Expr\Empty_ $node) {
        return 'empty(' . $this->p($node->expr) . ')';
    }

    protected function pExpr_Isset(Expr\Isset_ $node) {
        return 'isset(' . $this->pCommaSeparated($node->vars) . ')';
    }

    protected function pExpr_Eval(Expr\Eval_ $node) {
        return 'eval(' . $this->p($node->expr) . ')';
    }

    protected function pExpr_Include(Expr\Include_ $node) {
        static $map = array(
            Expr\Include_::TYPE_INCLUDE      => 'include',
            Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
            Expr\Include_::TYPE_REQUIRE      => 'require',
            Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
        );

        return $map[$node->type] . ' ' . $this->p($node->expr);
    }

    protected function pExpr_List(Expr\List_ $node) {
        return 'list(' . $this->pCommaSeparated($node->items) . ')';
    }

    // Other

    protected function pExpr_Error(Expr\Error $node) {
        throw new \LogicException('Cannot pretty-print AST with Error nodes');
    }

    protected function pExpr_Variable(Expr\Variable $node) {
        if ($node->name instanceof Expr) {
            return '${' . $this->p($node->name) . '}';
        } else {
            return '$' . $node->name;
        }
    }

    protected function pExpr_Array(Expr\Array_ $node) {
        $syntax = $node->getAttribute('kind',
            $this->options['shortArraySyntax'] ? Expr\Array_::KIND_SHORT : Expr\Array_::KIND_LONG);
        if ($syntax === Expr\Array_::KIND_SHORT) {
            return '[' . $this->pMaybeMultiline($node->items, true) . ']';
        } else {
            return 'array(' . $this->pMaybeMultiline($node->items, true) . ')';
        }
    }

    protected function pExpr_ArrayItem(Expr\ArrayItem $node) {
        return (null !== $node->key ? $this->p($node->key) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->value);
    }

    protected function pExpr_ArrayDimFetch(Expr\ArrayDimFetch $node) {
        return $this->pDereferenceLhs($node->var)
             . '[' . (null !== $node->dim ? $this->p($node->dim) : '') . ']';
    }

    protected function pExpr_ConstFetch(Expr\ConstFetch $node) {
        return $this->p($node->name);
    }

    protected function pExpr_ClassConstFetch(Expr\ClassConstFetch $node) {
        return $this->p($node->class) . '::'
             . (is_string($node->name) ? $node->name : $this->p($node->name));
    }

    protected function pExpr_PropertyFetch(Expr\PropertyFetch $node) {
        return $this->pDereferenceLhs($node->var) . '->' . $this->pObjectProperty($node->name);
    }

    protected function pExpr_StaticPropertyFetch(Expr\StaticPropertyFetch $node) {
        return $this->pDereferenceLhs($node->class) . '::$' . $this->pObjectProperty($node->name);
    }

    protected function pExpr_ShellExec(Expr\ShellExec $node) {
        return '`' . $this->pEncapsList($node->parts, '`') . '`';
    }

    protected function pExpr_Closure(Expr\Closure $node) {
        return ($node->static ? 'static ' : '')
             . 'function ' . ($node->byRef ? '&' : '')
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (!empty($node->uses) ? ' use(' . $this->pCommaSeparated($node->uses) . ')': '')
             . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
             . ' {' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pExpr_ClosureUse(Expr\ClosureUse $node) {
        return ($node->byRef ? '&' : '') . '$' . $node->var;
    }

    protected function pExpr_New(Expr\New_ $node) {
        if ($node->class instanceof Stmt\Class_) {
            $args = $node->args ? '(' . $this->pMaybeMultiline($node->args) . ')' : '';
            return 'new ' . $this->pClassCommon($node->class, $args);
        }
        return 'new ' . $this->p($node->class) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }

    protected function pExpr_Clone(Expr\Clone_ $node) {
        return 'clone ' . $this->p($node->expr);
    }

    protected function pExpr_Ternary(Expr\Ternary $node) {
        // a bit of cheating: we treat the ternary as a binary op where the ?...: part is the operator.
        // this is okay because the part between ? and : never needs parentheses.
        return $this->pInfixOp('Expr_Ternary',
            $node->cond, ' ?' . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '') . ': ', $node->else
        );
    }

    protected function pExpr_Exit(Expr\Exit_ $node) {
        $kind = $node->getAttribute('kind', Expr\Exit_::KIND_DIE);
        return ($kind === Expr\Exit_::KIND_EXIT ? 'exit' : 'die')
             . (null !== $node->expr ? '(' . $this->p($node->expr) . ')' : '');
    }

    protected function pExpr_Yield(Expr\Yield_ $node) {
        if ($node->value === null) {
            return 'yield';
        } else {
            // this is a bit ugly, but currently there is no way to detect whether the parentheses are necessary
            return '(yield '
                 . ($node->key !== null ? $this->p($node->key) . ' => ' : '')
                 . $this->p($node->value)
                 . ')';
        }
    }

    // Declarations

    protected function pStmt_Namespace(Stmt\Namespace_ $node) {
        if ($this->canUseSemicolonNamespaces) {
            return 'namespace ' . $this->p($node->name) . ';' . "\n" . $this->pStmts($node->stmts, false);
        } else {
            return 'namespace' . (null !== $node->name ? ' ' . $this->p($node->name) : '')
                 . ' {' . $this->pStmts($node->stmts) . "\n" . '}';
        }
    }

    protected function pStmt_Use(Stmt\Use_ $node) {
        return 'use ' . $this->pUseType($node->type)
             . $this->pCommaSeparated($node->uses) . ';';
    }

    protected function pStmt_GroupUse(Stmt\GroupUse $node) {
        return 'use ' . $this->pUseType($node->type) . $this->pName($node->prefix)
             . '\{' . $this->pCommaSeparated($node->uses) . '};';
    }

    protected function pStmt_UseUse(Stmt\UseUse $node) {
        return $this->pUseType($node->type) . $this->p($node->name)
             . ($node->name->getLast() !== $node->alias ? ' as ' . $node->alias : '');
    }

    protected function pUseType($type) {
        return $type === Stmt\Use_::TYPE_FUNCTION ? 'function '
            : ($type === Stmt\Use_::TYPE_CONSTANT ? 'const ' : '');
    }

    protected function pStmt_Interface(Stmt\Interface_ $node) {
        return 'interface ' . $node->name
             . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '')
             . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Class(Stmt\Class_ $node) {
        return $this->pClassCommon($node, ' ' . $node->name);
    }

    protected function pStmt_Trait(Stmt\Trait_ $node) {
        return 'trait ' . $node->name
             . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_TraitUse(Stmt\TraitUse $node) {
        return 'use ' . $this->pCommaSeparated($node->traits)
             . (empty($node->adaptations)
                ? ';'
                : ' {' . $this->pStmts($node->adaptations) . "\n" . '}');
    }

    protected function pStmt_TraitUseAdaptation_Precedence(Stmt\TraitUseAdaptation\Precedence $node) {
        return $this->p($node->trait) . '::' . $node->method
             . ' insteadof ' . $this->pCommaSeparated($node->insteadof) . ';';
    }

    protected function pStmt_TraitUseAdaptation_Alias(Stmt\TraitUseAdaptation\Alias $node) {
        return (null !== $node->trait ? $this->p($node->trait) . '::' : '')
             . $node->method . ' as'
             . (null !== $node->newModifier ? ' ' . rtrim($this->pModifiers($node->newModifier), ' ') : '')
             . (null !== $node->newName     ? ' ' . $node->newName                        : '')
             . ';';
    }

    protected function pStmt_Property(Stmt\Property $node) {
        return (0 === $node->flags ? 'var ' : $this->pModifiers($node->flags)) . $this->pCommaSeparated($node->props) . ';';
    }

    protected function pStmt_PropertyProperty(Stmt\PropertyProperty $node) {
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }

    protected function pStmt_ClassMethod(Stmt\ClassMethod $node) {
        return $this->pModifiers($node->flags)
             . 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
             . (null !== $node->stmts
                ? "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}'
                : ';');
    }

    protected function pStmt_ClassConst(Stmt\ClassConst $node) {
        return $this->pModifiers($node->flags)
             . 'const ' . $this->pCommaSeparated($node->consts) . ';';
    }

    protected function pStmt_Function(Stmt\Function_ $node) {
        return 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
             . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Const(Stmt\Const_ $node) {
        return 'const ' . $this->pCommaSeparated($node->consts) . ';';
    }

    protected function pStmt_Declare(Stmt\Declare_ $node) {
        return 'declare (' . $this->pCommaSeparated($node->declares) . ')'
             . (null !== $node->stmts ? ' {' . $this->pStmts($node->stmts) . "\n" . '}' : ';');
    }

    protected function pStmt_DeclareDeclare(Stmt\DeclareDeclare $node) {
        return $node->key . '=' . $this->p($node->value);
    }

    // Control flow

    protected function pStmt_If(Stmt\If_ $node) {
        return 'if (' . $this->p($node->cond) . ') {'
             . $this->pStmts($node->stmts) . "\n" . '}'
             . $this->pImplode($node->elseifs)
             . (null !== $node->else ? $this->p($node->else) : '');
    }

    protected function pStmt_ElseIf(Stmt\ElseIf_ $node) {
        return ' elseif (' . $this->p($node->cond) . ') {'
             . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Else(Stmt\Else_ $node) {
        return ' else {' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_For(Stmt\For_ $node) {
        return 'for ('
             . $this->pCommaSeparated($node->init) . ';' . (!empty($node->cond) ? ' ' : '')
             . $this->pCommaSeparated($node->cond) . ';' . (!empty($node->loop) ? ' ' : '')
             . $this->pCommaSeparated($node->loop)
             . ') {' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Foreach(Stmt\Foreach_ $node) {
        return 'foreach (' . $this->p($node->expr) . ' as '
             . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {'
             . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_While(Stmt\While_ $node) {
        return 'while (' . $this->p($node->cond) . ') {'
             . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Do(Stmt\Do_ $node) {
        return 'do {' . $this->pStmts($node->stmts) . "\n"
             . '} while (' . $this->p($node->cond) . ');';
    }

    protected function pStmt_Switch(Stmt\Switch_ $node) {
        return 'switch (' . $this->p($node->cond) . ') {'
             . $this->pStmts($node->cases) . "\n" . '}';
    }

    protected function pStmt_Case(Stmt\Case_ $node) {
        return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':'
             . $this->pStmts($node->stmts);
    }

    protected function pStmt_TryCatch(Stmt\TryCatch $node) {
        return 'try {' . $this->pStmts($node->stmts) . "\n" . '}'
             . $this->pImplode($node->catches)
             . ($node->finally !== null ? $this->p($node->finally) : '');
    }

    protected function pStmt_Catch(Stmt\Catch_ $node) {
        return ' catch (' . $this->pImplode($node->types, '|') . ' $' . $node->var . ') {'
             . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Finally(Stmt\Finally_ $node) {
        return ' finally {' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pStmt_Break(Stmt\Break_ $node) {
        return 'break' . ($node->num !== null ? ' ' . $this->p($node->num) : '') . ';';
    }

    protected function pStmt_Continue(Stmt\Continue_ $node) {
        return 'continue' . ($node->num !== null ? ' ' . $this->p($node->num) : '') . ';';
    }

    protected function pStmt_Return(Stmt\Return_ $node) {
        return 'return' . (null !== $node->expr ? ' ' . $this->p($node->expr) : '') . ';';
    }

    protected function pStmt_Throw(Stmt\Throw_ $node) {
        return 'throw ' . $this->p($node->expr) . ';';
    }

    protected function pStmt_Label(Stmt\Label $node) {
        return $node->name . ':';
    }

    protected function pStmt_Goto(Stmt\Goto_ $node) {
        return 'goto ' . $node->name . ';';
    }

    // Other

    protected function pStmt_Echo(Stmt\Echo_ $node) {
        return 'echo ' . $this->pCommaSeparated($node->exprs) . ';';
    }

    protected function pStmt_Static(Stmt\Static_ $node) {
        return 'static ' . $this->pCommaSeparated($node->vars) . ';';
    }

    protected function pStmt_Global(Stmt\Global_ $node) {
        return 'global ' . $this->pCommaSeparated($node->vars) . ';';
    }

    protected function pStmt_StaticVar(Stmt\StaticVar $node) {
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }

    protected function pStmt_Unset(Stmt\Unset_ $node) {
        return 'unset(' . $this->pCommaSeparated($node->vars) . ');';
    }

    protected function pStmt_InlineHTML(Stmt\InlineHTML $node) {
        $newline = $node->getAttribute('hasLeadingNewline', true) ? "\n" : '';
        return '?>' . $this->pNoIndent($newline . $node->value) . '<?php ';
    }

    protected function pStmt_HaltCompiler(Stmt\HaltCompiler $node) {
        return '__halt_compiler();' . $node->remaining;
    }

    protected function pStmt_Nop(Stmt\Nop $node) {
        return '';
    }

    // Helpers

    protected function pType($node) {
        return is_string($node) ? $node : $this->p($node);
    }

    protected function pClassCommon(Stmt\Class_ $node, $afterClassToken) {
        return $this->pModifiers($node->flags)
        . 'class' . $afterClassToken
        . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
        . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
        . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
    }

    protected function pObjectProperty($node) {
        if ($node instanceof Expr) {
            return '{' . $this->p($node) . '}';
        } else {
            return $node;
        }
    }

    protected function pModifiers($modifiers) {
        return ($modifiers & Stmt\Class_::MODIFIER_PUBLIC    ? 'public '    : '')
             . ($modifiers & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '')
             . ($modifiers & Stmt\Class_::MODIFIER_PRIVATE   ? 'private '   : '')
             . ($modifiers & Stmt\Class_::MODIFIER_STATIC    ? 'static '    : '')
             . ($modifiers & Stmt\Class_::MODIFIER_ABSTRACT  ? 'abstract '  : '')
             . ($modifiers & Stmt\Class_::MODIFIER_FINAL     ? 'final '     : '');
    }

    protected function pEncapsList(array $encapsList, $quote) {
        $return = '';
        foreach ($encapsList as $element) {
            if ($element instanceof Scalar\EncapsedStringPart) {
                $return .= $this->escapeString($element->value, $quote);
            } else {
                $return .= '{' . $this->p($element) . '}';
            }
        }

        return $return;
    }

    protected function escapeString($string, $quote) {
        if (null === $quote) {
            // For doc strings, don't escape newlines
            $escaped = addcslashes($string, "\t\f\v$\\");
        } else {
            $escaped = addcslashes($string, "\n\r\t\f\v$" . $quote . "\\");
        }

        // Escape other control characters
        return preg_replace_callback('/([\0-\10\16-\37])(?=([0-7]?))/', function ($matches) {
            $oct = decoct(ord($matches[1]));
            if ($matches[2] !== '') {
                // If there is a trailing digit, use the full three character form
                return '\\' . str_pad($oct, 3, '0', STR_PAD_LEFT);
            }
            return '\\' . $oct;
        }, $escaped);
    }

    protected function containsEndLabel($string, $label, $atStart = true, $atEnd = true) {
        $start = $atStart ? '(?:^|[\r\n])' : '[\r\n]';
        $end = $atEnd ? '(?:$|[;\r\n])' : '[;\r\n]';
        return false !== strpos($string, $label)
            && preg_match('/' . $start . $label . $end . '/', $string);
    }

    protected function encapsedContainsEndLabel(array $parts, $label) {
        foreach ($parts as $i => $part) {
            $atStart = $i === 0;
            $atEnd = $i === count($parts) - 1;
            if ($part instanceof Scalar\EncapsedStringPart
                && $this->containsEndLabel($part->value, $label, $atStart, $atEnd)
            ) {
                return true;
            }
        }
        return false;
    }

    protected function pDereferenceLhs(Node $node) {
        if ($node instanceof Expr\Variable
            || $node instanceof Name
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\StaticPropertyFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
            || $node instanceof Scalar\String_
            || $node instanceof Expr\ConstFetch
            || $node instanceof Expr\ClassConstFetch
        ) {
            return $this->p($node);
        } else  {
            return '(' . $this->p($node) . ')';
        }
    }

    protected function pCallLhs(Node $node) {
        if ($node instanceof Name
            || $node instanceof Expr\Variable
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
        ) {
            return $this->p($node);
        } else  {
            return '(' . $this->p($node) . ')';
        }
    }

    private function hasNodeWithComments(array $nodes) {
        foreach ($nodes as $node) {
            if ($node && $node->getAttribute('comments')) {
                return true;
            }
        }
        return false;
    }

    private function pMaybeMultiline(array $nodes, $trailingComma = false) {
        if (!$this->hasNodeWithComments($nodes)) {
            return $this->pCommaSeparated($nodes);
        } else {
            return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . "\n";
        }
    }
}
