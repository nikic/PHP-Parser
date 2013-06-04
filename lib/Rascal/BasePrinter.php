<?php
class BasePrinter implements IPrinter
{
	public function pprint(PHPParser_Node $node)
	{
		if ($node instanceof PHPParser_Node_Arg) {
			return $this->pprintArg($node);
		} elseif ($node instanceof PHPParser_Node_Const) {
			return $this->pprintConst($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Array) {
			return $this->pprintArrayExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayDimFetch) {
			return $this->pprintArrayDimFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayItem) {
			return $this->pprintArrayItemExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Assign) {
			return $this->pprintAssignExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseAnd) {
			return $this->pprintAssignBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseOr) {
			return $this->pprintAssignBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseXor) {
			return $this->pprintAssignBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignConcat) {
			return $this->pprintAssignConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignDiv) {
			return $this->pprintAssignDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMinus) {
			return $this->pprintAssignMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMod) {
			return $this->pprintAssignModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMul) {
			return $this->pprintAssignMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignPlus) {
			return $this->pprintAssignPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignRef) {
			return $this->pprintAssignRefExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftLeft) {
			return $this->pprintAssignShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftRight) {
			return $this->pprintAssignShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseAnd) {
			return $this->pprintBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseNot) {
			return $this->pprintBitwiseNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseOr) {
			return $this->pprintBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseXor) {
			return $this->pprintBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanAnd) {
			return $this->pprintBooleanAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanNot) {
			return $this->pprintBooleanNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanOr) {
			return $this->pprintBooleanOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Array) {
			return $this->pprintArrayCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Bool) {
			return $this->pprintBoolCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Double) {
			return $this->pprintDoubleCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Int) {
			return $this->pprintIntCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Object) {
			return $this->pprintObjectCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_String) {
			return $this->pprintStringCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Unset) {
			return $this->pprintUnsetCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast) {
			return $this->pprintCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClassConstFetch) {
			return $this->pprintClassConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Clone) {
			return $this->pprintCloneExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Closure) {
			return $this->pprintClosureExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClosureUse) {
			return $this->pprintClosureUseExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Concat) {
			return $this->pprintConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ConstFetch) {
			return $this->pprintConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Div) {
			return $this->pprintDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Empty) {
			return $this->pprintEmptyExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Equal) {
			return $this->pprintEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ErrorSuppress) {
			return $this->pprintErrorSuppressExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Eval) {
			return $this->pprintEvalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Exit) {
			return $this->pprintExitExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_FuncCall) {
			return $this->pprintFuncCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Greater) {
			return $this->pprintGreaterExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_GreaterOrEqual) {
			return $this->pprintGreaterOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Identical) {
			return $this->pprintIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Include) {
			return $this->pprintIncludeExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Instanceof) {
			return $this->pprintInstanceofExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Isset) {
			return $this->pprintIssetExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_List) {
			return $this->pprintListExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalAnd) {
			return $this->pprintLogicalAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalOr) {
			return $this->pprintLogicalOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalXor) {
			return $this->pprintLogicalXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_MethodCall) {
			return $this->pprintMethodCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Minus) {
			return $this->pprintMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mod) {
			return $this->pprintModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mul) {
			return $this->pprintMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_New) {
			return $this->pprintNewExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotEqual) {
			return $this->pprintNotEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotIdentical) {
			return $this->pprintNotIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Plus) {
			return $this->pprintPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostDec) {
			return $this->pprintPostDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostInc) {
			return $this->pprintPostIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreDec) {
			return $this->pprintPreDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreInc) {
			return $this->pprintPreIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Print) {
			return $this->pprintPrintExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PropertyFetch) {
			return $this->pprintPropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShellExec) {
			return $this->pprintShellExecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftLeft) {
			return $this->pprintShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftRight) {
			return $this->pprintShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Smaller) {
			return $this->pprintSmallerExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_SmallerOrEqual) {
			return $this->pprintSmallerOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticCall) {
			return $this->pprintStaticCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticPropertyFetch) {
			return $this->pprintStaticPropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Ternary) {
			return $this->pprintTernaryExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryMinus) {
			return $this->pprintUnaryMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryPlus) {
			return $this->pprintUnaryPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Variable) {
			return $this->pprintVariableExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Yield) {
			return $this->pprintYieldExpr($node);
		} elseif ($node instanceof PHPParser_Node_Name_FullyQualified) {
			return $this->pprintFullyQualifiedName($node);
		} elseif ($node instanceof PHPParser_Node_Name_Relative) {
			return $this->pprintRelativeName($node);
		} elseif ($node instanceof PHPParser_Node_Name) {
			return $this->pprintName($node);
		} elseif ($node instanceof PHPParser_Node_Param) {
			return $this->pprintParam($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_ClassConst) {
			return $this->pprintClassConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DirConst) {
			return $this->pprintDirConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DNumber) {
			return $this->pprintDNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_Encapsed) {
			return $this->pprintEncapsedScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FileConst) {
			return $this->pprintFileConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FuncConst) {
			return $this->pprintFuncConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LineConst) {
			return $this->pprintLineConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LNumber) {
			return $this->pprintLNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_MethodConst) {
			return $this->pprintMethodConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_NSConst) {
			return $this->pprintNSConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_String) {
			return $this->pprintStringScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_TraitConst) {
			return $this->pprintTraitConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar) {
			return $this->pprintScalar($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Break) {
			return $this->pprintBreakStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Case) {
			return $this->pprintCaseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Catch) {
			return $this->pprintCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Class) {
			return $this->pprintClassStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassConst) {
			return $this->pprintClassConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassMethod) {
			return $this->pprintClassMethodStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Const) {
			return $this->pprintConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Continue) {
			return $this->pprintContinueStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Declare) {
			return $this->pprintDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_DeclareDeclare) {
			return $this->pprintDeclareDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Do) {
			return $this->pprintDoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Echo) {
			return $this->pprintEchoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Else) {
			return $this->pprintElseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ElseIf) {
			return $this->pprintElseIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Expr) {
			return $this->pprintExprStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_For) {
			return $this->pprintForStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Foreach) {
			return $this->pprintForeachStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Function) {
			return $this->pprintFunctionStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Global) {
			return $this->pprintGlobalStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Goto) {
			return $this->pprintGotoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_HaltCompiler) {
			return $this->pprintHaltCompilerStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_If) {
			return $this->pprintIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_InlineHTML) {
			return $this->pprintInlineHTMLStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
			return $this->pprintInterfaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Label) {
			return $this->pprintLabelStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Namespace) {
			return $this->pprintNamespaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Property) {
			return $this->pprintPropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_PropertyProperty) {
			return $this->pprintPropertyPropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Return) {
			return $this->pprintReturnStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Static) {
			return $this->pprintStaticStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_StaticVar) {
			return $this->pprintStaticVarStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Switch) {
			return $this->pprintSwitchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Throw) {
			return $this->pprintThrowStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Trait) {
			return $this->pprintTraitStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUse) {
			return $this->pprintTraitUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Alias) {
			return $this->pprintAliasTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Precedence) {
			return $this->pprintPrecedenceTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation) {
			return $this->pprintTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TryCatch) {
			return $this->pprintTryCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Unset) {
			return $this->pprintUnsetStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Use) {
			return $this->pprintUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_UseUse) {
			return $this->pprintUseUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_While) {
			return $this->pprintWhileStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt) {
			return $this->pprintStmt($node);
		} elseif ($node instanceof PHPParser_Node_Expr) {
			return $this->pprintExpr($node);
		}
	}
	public function pprintArg(PHPParser_Node_Arg $node)
	{
		return "";
	}
	public function pprintConst(PHPParser_Node_Const $node)
	{
		return "";
	}
	public function pprintArrayExpr(PHPParser_Node_Expr_Array $node)
	{
		return "";
	}
	public function pprintArrayDimFetchExpr(PHPParser_Node_Expr_ArrayDimFetch $node)
	{
		return "";
	}
	public function pprintArrayItemExpr(PHPParser_Node_Expr_ArrayItem $node)
	{
		return "";
	}
	public function pprintAssignExpr(PHPParser_Node_Expr_Assign $node)
	{
		return "";
	}
	public function pprintAssignBitwiseAndExpr(PHPParser_Node_Expr_AssignBitwiseAnd $node)
	{
		return "";
	}
	public function pprintAssignBitwiseOrExpr(PHPParser_Node_Expr_AssignBitwiseOr $node)
	{
		return "";
	}
	public function pprintAssignBitwiseXorExpr(PHPParser_Node_Expr_AssignBitwiseXor $node)
	{
		return "";
	}
	public function pprintAssignConcatExpr(PHPParser_Node_Expr_AssignConcat $node)
	{
		return "";
	}
	public function pprintAssignDivExpr(PHPParser_Node_Expr_AssignDiv $node)
	{
		return "";
	}
	public function pprintAssignMinusExpr(PHPParser_Node_Expr_AssignMinus $node)
	{
		return "";
	}
	public function pprintAssignModExpr(PHPParser_Node_Expr_AssignMod $node)
	{
		return "";
	}
	public function pprintAssignMulExpr(PHPParser_Node_Expr_AssignMul $node)
	{
		return "";
	}
	public function pprintAssignPlusExpr(PHPParser_Node_Expr_AssignPlus $node)
	{
		return "";
	}
	public function pprintAssignRefExpr(PHPParser_Node_Expr_AssignRef $node)
	{
		return "";
	}
	public function pprintAssignShiftLeftExpr(PHPParser_Node_Expr_AssignShiftLeft $node)
	{
		return "";
	}
	public function pprintAssignShiftRightExpr(PHPParser_Node_Expr_AssignShiftRight $node)
	{
		return "";
	}
	public function pprintBitwiseAndExpr(PHPParser_Node_Expr_BitwiseAnd $node)
	{
		return "";
	}
	public function pprintBitwiseNotExpr(PHPParser_Node_Expr_BitwiseNot $node)
	{
		return "";
	}
	public function pprintBitwiseOrExpr(PHPParser_Node_Expr_BitwiseOr $node)
	{
		return "";
	}
	public function pprintBitwiseXorExpr(PHPParser_Node_Expr_BitwiseXor $node)
	{
		return "";
	}
	public function pprintBooleanAndExpr(PHPParser_Node_Expr_BooleanAnd $node)
	{
		return "";
	}
	public function pprintBooleanNotExpr(PHPParser_Node_Expr_BooleanNot $node)
	{
		return "";
	}
	public function pprintBooleanOrExpr(PHPParser_Node_Expr_BooleanOr $node)
	{
		return "";
	}
	public function pprintArrayCastExpr(PHPParser_Node_Expr_Cast_Array $node)
	{
		return "";
	}
	public function pprintBoolCastExpr(PHPParser_Node_Expr_Cast_Bool $node)
	{
		return "";
	}
	public function pprintDoubleCastExpr(PHPParser_Node_Expr_Cast_Double $node)
	{
		return "";
	}
	public function pprintIntCastExpr(PHPParser_Node_Expr_Cast_Int $node)
	{
		return "";
	}
	public function pprintObjectCastExpr(PHPParser_Node_Expr_Cast_Object $node)
	{
		return "";
	}
	public function pprintStringCastExpr(PHPParser_Node_Expr_Cast_String $node)
	{
		return "";
	}
	public function pprintUnsetCastExpr(PHPParser_Node_Expr_Cast_Unset $node)
	{
		return "";
	}
	public function pprintCastExpr(PHPParser_Node_Expr_Cast $node)
	{
		return "";
	}
	public function pprintClassConstFetchExpr(PHPParser_Node_Expr_ClassConstFetch $node)
	{
		return "";
	}
	public function pprintCloneExpr(PHPParser_Node_Expr_Clone $node)
	{
		return "";
	}
	public function pprintClosureExpr(PHPParser_Node_Expr_Closure $node)
	{
		return "";
	}
	public function pprintClosureUseExpr(PHPParser_Node_Expr_ClosureUse $node)
	{
		return "";
	}
	public function pprintConcatExpr(PHPParser_Node_Expr_Concat $node)
	{
		return "";
	}
	public function pprintConstFetchExpr(PHPParser_Node_Expr_ConstFetch $node)
	{
		return "";
	}
	public function pprintDivExpr(PHPParser_Node_Expr_Div $node)
	{
		return "";
	}
	public function pprintEmptyExpr(PHPParser_Node_Expr_Empty $node)
	{
		return "";
	}
	public function pprintEqualExpr(PHPParser_Node_Expr_Equal $node)
	{
		return "";
	}
	public function pprintErrorSuppressExpr(PHPParser_Node_Expr_ErrorSuppress $node)
	{
		return "";
	}
	public function pprintEvalExpr(PHPParser_Node_Expr_Eval $node)
	{
		return "";
	}
	public function pprintExitExpr(PHPParser_Node_Expr_Exit $node)
	{
		return "";
	}
	public function pprintFuncCallExpr(PHPParser_Node_Expr_FuncCall $node)
	{
		return "";
	}
	public function pprintGreaterExpr(PHPParser_Node_Expr_Greater $node)
	{
		return "";
	}
	public function pprintGreaterOrEqualExpr(PHPParser_Node_Expr_GreaterOrEqual $node)
	{
		return "";
	}
	public function pprintIdenticalExpr(PHPParser_Node_Expr_Identical $node)
	{
		return "";
	}
	public function pprintIncludeExpr(PHPParser_Node_Expr_Include $node)
	{
		return "";
	}
	public function pprintInstanceofExpr(PHPParser_Node_Expr_Instanceof $node)
	{
		return "";
	}
	public function pprintIssetExpr(PHPParser_Node_Expr_Isset $node)
	{
		return "";
	}
	public function pprintListExpr(PHPParser_Node_Expr_List $node)
	{
		return "";
	}
	public function pprintLogicalAndExpr(PHPParser_Node_Expr_LogicalAnd $node)
	{
		return "";
	}
	public function pprintLogicalOrExpr(PHPParser_Node_Expr_LogicalOr $node)
	{
		return "";
	}
	public function pprintLogicalXorExpr(PHPParser_Node_Expr_LogicalXor $node)
	{
		return "";
	}
	public function pprintMethodCallExpr(PHPParser_Node_Expr_MethodCall $node)
	{
		return "";
	}
	public function pprintMinusExpr(PHPParser_Node_Expr_Minus $node)
	{
		return "";
	}
	public function pprintModExpr(PHPParser_Node_Expr_Mod $node)
	{
		return "";
	}
	public function pprintMulExpr(PHPParser_Node_Expr_Mul $node)
	{
		return "";
	}
	public function pprintNewExpr(PHPParser_Node_Expr_New $node)
	{
		return "";
	}
	public function pprintNotEqualExpr(PHPParser_Node_Expr_NotEqual $node)
	{
		return "";
	}
	public function pprintNotIdenticalExpr(PHPParser_Node_Expr_NotIdentical $node)
	{
		return "";
	}
	public function pprintPlusExpr(PHPParser_Node_Expr_Plus $node)
	{
		return "";
	}
	public function pprintPostDecExpr(PHPParser_Node_Expr_PostDec $node)
	{
		return "";
	}
	public function pprintPostIncExpr(PHPParser_Node_Expr_PostInc $node)
	{
		return "";
	}
	public function pprintPreDecExpr(PHPParser_Node_Expr_PreDec $node)
	{
		return "";
	}
	public function pprintPreIncExpr(PHPParser_Node_Expr_PreInc $node)
	{
		return "";
	}
	public function pprintPrintExpr(PHPParser_Node_Expr_Print $node)
	{
		return "";
	}
	public function pprintPropertyFetchExpr(PHPParser_Node_Expr_PropertyFetch $node)
	{
		return "";
	}
	public function pprintShellExecExpr(PHPParser_Node_Expr_ShellExec $node)
	{
		return "";
	}
	public function pprintShiftLeftExpr(PHPParser_Node_Expr_ShiftLeft $node)
	{
		return "";
	}
	public function pprintShiftRightExpr(PHPParser_Node_Expr_ShiftRight $node)
	{
		return "";
	}
	public function pprintSmallerExpr(PHPParser_Node_Expr_Smaller $node)
	{
		return "";
	}
	public function pprintSmallerOrEqualExpr(PHPParser_Node_Expr_SmallerOrEqual $node)
	{
		return "";
	}
	public function pprintStaticCallExpr(PHPParser_Node_Expr_StaticCall $node)
	{
		return "";
	}
	public function pprintStaticPropertyFetchExpr(PHPParser_Node_Expr_StaticPropertyFetch $node)
	{
		return "";
	}
	public function pprintTernaryExpr(PHPParser_Node_Expr_Ternary $node)
	{
		return "";
	}
	public function pprintUnaryMinusExpr(PHPParser_Node_Expr_UnaryMinus $node)
	{
		return "";
	}
	public function pprintUnaryPlusExpr(PHPParser_Node_Expr_UnaryPlus $node)
	{
		return "";
	}
	public function pprintVariableExpr(PHPParser_Node_Expr_Variable $node)
	{
		return "";
	}
	public function pprintYieldExpr(PHPParser_Node_Expr_Yield $node)
	{
		return "";
	}
	public function pprintFullyQualifiedName(PHPParser_Node_Name_FullyQualified $node)
	{
		return "";
	}
	public function pprintRelativeName(PHPParser_Node_Name_Relative $node)
	{
		return "";
	}
	public function pprintName(PHPParser_Node_Name $node)
	{
		return "";
	}
	public function pprintParam(PHPParser_Node_Param $node)
	{
		return "";
	}
	public function pprintClassConstScalar(PHPParser_Node_Scalar_ClassConst $node)
	{
		return "";
	}
	public function pprintDirConstScalar(PHPParser_Node_Scalar_DirConst $node)
	{
		return "";
	}
	public function pprintDNumberScalar(PHPParser_Node_Scalar_DNumber $node)
	{
		return "";
	}
	public function pprintEncapsedScalar(PHPParser_Node_Scalar_Encapsed $node)
	{
		return "";
	}
	public function pprintFileConstScalar(PHPParser_Node_Scalar_FileConst $node)
	{
		return "";
	}
	public function pprintFuncConstScalar(PHPParser_Node_Scalar_FuncConst $node)
	{
		return "";
	}
	public function pprintLineConstScalar(PHPParser_Node_Scalar_LineConst $node)
	{
		return "";
	}
	public function pprintLNumberScalar(PHPParser_Node_Scalar_LNumber $node)
	{
		return "";
	}
	public function pprintMethodConstScalar(PHPParser_Node_Scalar_MethodConst $node)
	{
		return "";
	}
	public function pprintNSConstScalar(PHPParser_Node_Scalar_NSConst $node)
	{
		return "";
	}
	public function pprintStringScalar(PHPParser_Node_Scalar_String $node)
	{
		return "";
	}
	public function pprintTraitConstScalar(PHPParser_Node_Scalar_TraitConst $node)
	{
		return "";
	}
	public function pprintScalar(PHPParser_Node_Scalar $node)
	{
		return "";
	}
	public function pprintBreakStmt(PHPParser_Node_Stmt_Break $node)
	{
		return "";
	}
	public function pprintCaseStmt(PHPParser_Node_Stmt_Case $node)
	{
		return "";
	}
	public function pprintCatchStmt(PHPParser_Node_Stmt_Catch $node)
	{
		return "";
	}
	public function pprintClassStmt(PHPParser_Node_Stmt_Class $node)
	{
		return "";
	}
	public function pprintClassConstStmt(PHPParser_Node_Stmt_ClassConst $node)
	{
		return "";
	}
	public function pprintClassMethodStmt(PHPParser_Node_Stmt_ClassMethod $node)
	{
		return "";
	}
	public function pprintConstStmt(PHPParser_Node_Stmt_Const $node)
	{
		return "";
	}
	public function pprintContinueStmt(PHPParser_Node_Stmt_Continue $node)
	{
		return "";
	}
	public function pprintDeclareStmt(PHPParser_Node_Stmt_Declare $node)
	{
		return "";
	}
	public function pprintDeclareDeclareStmt(PHPParser_Node_Stmt_DeclareDeclare $node)
	{
		return "";
	}
	public function pprintDoStmt(PHPParser_Node_Stmt_Do $node)
	{
		return "";
	}
	public function pprintEchoStmt(PHPParser_Node_Stmt_Echo $node)
	{
		return "";
	}
	public function pprintElseStmt(PHPParser_Node_Stmt_Else $node)
	{
		return "";
	}
	public function pprintElseIfStmt(PHPParser_Node_Stmt_ElseIf $node)
	{
		return "";
	}
	public function pprintExprStmt(PHPParser_Node_Stmt_Expr $node)
	{
		return "";
	}
	public function pprintForStmt(PHPParser_Node_Stmt_For $node)
	{
		return "";
	}
	public function pprintForeachStmt(PHPParser_Node_Stmt_Foreach $node)
	{
		return "";
	}
	public function pprintFunctionStmt(PHPParser_Node_Stmt_Function $node)
	{
		return "";
	}
	public function pprintGlobalStmt(PHPParser_Node_Stmt_Global $node)
	{
		return "";
	}
	public function pprintGotoStmt(PHPParser_Node_Stmt_Goto $node)
	{
		return "";
	}
	public function pprintHaltCompilerStmt(PHPParser_Node_Stmt_HaltCompiler $node)
	{
		return "";
	}
	public function pprintIfStmt(PHPParser_Node_Stmt_If $node)
	{
		return "";
	}
	public function pprintInlineHTMLStmt(PHPParser_Node_Stmt_InlineHTML $node)
	{
		return "";
	}
	public function pprintInterfaceStmt(PHPParser_Node_Stmt_Interface $node)
	{
		return "";
	}
	public function pprintLabelStmt(PHPParser_Node_Stmt_Label $node)
	{
		return "";
	}
	public function pprintNamespaceStmt(PHPParser_Node_Stmt_Namespace $node)
	{
		return "";
	}
	public function pprintPropertyStmt(PHPParser_Node_Stmt_Property $node)
	{
		return "";
	}
	public function pprintPropertyPropertyStmt(PHPParser_Node_Stmt_PropertyProperty $node)
	{
		return "";
	}
	public function pprintReturnStmt(PHPParser_Node_Stmt_Return $node)
	{
		return "";
	}
	public function pprintStaticStmt(PHPParser_Node_Stmt_Static $node)
	{
		return "";
	}
	public function pprintStaticVarStmt(PHPParser_Node_Stmt_StaticVar $node)
	{
		return "";
	}
	public function pprintSwitchStmt(PHPParser_Node_Stmt_Switch $node)
	{
		return "";
	}
	public function pprintThrowStmt(PHPParser_Node_Stmt_Throw $node)
	{
		return "";
	}
	public function pprintTraitStmt(PHPParser_Node_Stmt_Trait $node)
	{
		return "";
	}
	public function pprintTraitUseStmt(PHPParser_Node_Stmt_TraitUse $node)
	{
		return "";
	}
	public function pprintAliasTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Alias $node)
	{
		return "";
	}
	public function pprintPrecedenceTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Precedence $node)
	{
		return "";
	}
	public function pprintTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation $node)
	{
		return "";
	}
	public function pprintTryCatchStmt(PHPParser_Node_Stmt_TryCatch $node)
	{
		return "";
	}
	public function pprintUnsetStmt(PHPParser_Node_Stmt_Unset $node)
	{
		return "";
	}
	public function pprintUseStmt(PHPParser_Node_Stmt_Use $node)
	{
		return "";
	}
	public function pprintUseUseStmt(PHPParser_Node_Stmt_UseUse $node)
	{
		return "";
	}
	public function pprintWhileStmt(PHPParser_Node_Stmt_While $node)
	{
		return "";
	}
	public function pprintStmt(PHPParser_Node_Stmt $node)
	{
		return "";
	}
	public function pprintExpr(PHPParser_Node_Expr $node)
	{
		return "";
	}

}
?>