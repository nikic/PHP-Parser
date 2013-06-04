<?php
require_once('IVisitor.php');

class UsefulVisitor extends PHPParser_NodeVisitorAbstract
{
	private $visitor = null;

	public function UsefulVisitor(IVisitor $v)
	{
		$this->visitor = $v;
	}

	public function enterNode(PHPParser_Node $node)
	{
		if ($node instanceof PHPParser_Node_Arg) {
			return $this->visitor->enterArg($node);
		} elseif ($node instanceof PHPParser_Node_Const) {
			return $this->visitor->enterConst($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Array) {
			return $this->visitor->enterArrayExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayDimFetch) {
			return $this->visitor->enterArrayDimFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayItem) {
			return $this->visitor->enterArrayItemExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Assign) {
			return $this->visitor->enterAssignExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseAnd) {
			return $this->visitor->enterAssignBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseOr) {
			return $this->visitor->enterAssignBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseXor) {
			return $this->visitor->enterAssignBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignConcat) {
			return $this->visitor->enterAssignConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignDiv) {
			return $this->visitor->enterAssignDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMinus) {
			return $this->visitor->enterAssignMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMod) {
			return $this->visitor->enterAssignModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMul) {
			return $this->visitor->enterAssignMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignPlus) {
			return $this->visitor->enterAssignPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignRef) {
			return $this->visitor->enterAssignRefExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftLeft) {
			return $this->visitor->enterAssignShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftRight) {
			return $this->visitor->enterAssignShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseAnd) {
			return $this->visitor->enterBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseNot) {
			return $this->visitor->enterBitwiseNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseOr) {
			return $this->visitor->enterBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseXor) {
			return $this->visitor->enterBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanAnd) {
			return $this->visitor->enterBooleanAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanNot) {
			return $this->visitor->enterBooleanNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanOr) {
			return $this->visitor->enterBooleanOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Array) {
			return $this->visitor->enterArrayCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Bool) {
			return $this->visitor->enterBoolCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Double) {
			return $this->visitor->enterDoubleCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Int) {
			return $this->visitor->enterIntCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Object) {
			return $this->visitor->enterObjectCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_String) {
			return $this->visitor->enterStringCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Unset) {
			return $this->visitor->enterUnsetCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast) {
			return $this->visitor->enterCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClassConstFetch) {
			return $this->visitor->enterClassConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Clone) {
			return $this->visitor->enterCloneExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Closure) {
			return $this->visitor->enterClosureExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClosureUse) {
			return $this->visitor->enterClosureUseExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Concat) {
			return $this->visitor->enterConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ConstFetch) {
			return $this->visitor->enterConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Div) {
			return $this->visitor->enterDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Empty) {
			return $this->visitor->enterEmptyExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Equal) {
			return $this->visitor->enterEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ErrorSuppress) {
			return $this->visitor->enterErrorSuppressExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Eval) {
			return $this->visitor->enterEvalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Exit) {
			return $this->visitor->enterExitExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_FuncCall) {
			return $this->visitor->enterFuncCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Greater) {
			return $this->visitor->enterGreaterExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_GreaterOrEqual) {
			return $this->visitor->enterGreaterOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Identical) {
			return $this->visitor->enterIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Include) {
			return $this->visitor->enterIncludeExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Instanceof) {
			return $this->visitor->enterInstanceofExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Isset) {
			return $this->visitor->enterIssetExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_List) {
			return $this->visitor->enterListExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalAnd) {
			return $this->visitor->enterLogicalAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalOr) {
			return $this->visitor->enterLogicalOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalXor) {
			return $this->visitor->enterLogicalXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_MethodCall) {
			return $this->visitor->enterMethodCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Minus) {
			return $this->visitor->enterMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mod) {
			return $this->visitor->enterModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mul) {
			return $this->visitor->enterMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_New) {
			return $this->visitor->enterNewExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotEqual) {
			return $this->visitor->enterNotEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotIdentical) {
			return $this->visitor->enterNotIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Plus) {
			return $this->visitor->enterPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostDec) {
			return $this->visitor->enterPostDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostInc) {
			return $this->visitor->enterPostIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreDec) {
			return $this->visitor->enterPreDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreInc) {
			return $this->visitor->enterPreIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Print) {
			return $this->visitor->enterPrintExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PropertyFetch) {
			return $this->visitor->enterPropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShellExec) {
			return $this->visitor->enterShellExecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftLeft) {
			return $this->visitor->enterShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftRight) {
			return $this->visitor->enterShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Smaller) {
			return $this->visitor->enterSmallerExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_SmallerOrEqual) {
			return $this->visitor->enterSmallerOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticCall) {
			return $this->visitor->enterStaticCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticPropertyFetch) {
			return $this->visitor->enterStaticPropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Ternary) {
			return $this->visitor->enterTernaryExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryMinus) {
			return $this->visitor->enterUnaryMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryPlus) {
			return $this->visitor->enterUnaryPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Variable) {
			return $this->visitor->enterVariableExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Yield) {
			return $this->visitor->enterYieldExpr($node);
		} elseif ($node instanceof PHPParser_Node_Name_FullyQualified) {
			return $this->visitor->enterFullyQualifiedName($node);
		} elseif ($node instanceof PHPParser_Node_Name_Relative) {
			return $this->visitor->enterRelativeName($node);
		} elseif ($node instanceof PHPParser_Node_Name) {
			return $this->visitor->enterName($node);
		} elseif ($node instanceof PHPParser_Node_Param) {
			return $this->visitor->enterParam($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_ClassConst) {
			return $this->visitor->enterClassConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DirConst) {
			return $this->visitor->enterDirConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DNumber) {
			return $this->visitor->enterDNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_Encapsed) {
			return $this->visitor->enterEncapsedScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FileConst) {
			return $this->visitor->enterFileConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FuncConst) {
			return $this->visitor->enterFuncConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LineConst) {
			return $this->visitor->enterLineConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LNumber) {
			return $this->visitor->enterLNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_MethodConst) {
			return $this->visitor->enterMethodConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_NSConst) {
			return $this->visitor->enterNSConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_String) {
			return $this->visitor->enterStringScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_TraitConst) {
			return $this->visitor->enterTraitConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar) {
			return $this->visitor->enterScalar($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Break) {
			return $this->visitor->enterBreakStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Case) {
			return $this->visitor->enterCaseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Catch) {
			return $this->visitor->enterCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Class) {
			return $this->visitor->enterClassStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassConst) {
			return $this->visitor->enterClassConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassMethod) {
			return $this->visitor->enterClassMethodStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Const) {
			return $this->visitor->enterConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Continue) {
			return $this->visitor->enterContinueStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Declare) {
			return $this->visitor->enterDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_DeclareDeclare) {
			return $this->visitor->enterDeclareDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Do) {
			return $this->visitor->enterDoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Echo) {
			return $this->visitor->enterEchoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Else) {
			return $this->visitor->enterElseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ElseIf) {
			return $this->visitor->enterElseIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Expr) {
			return $this->visitor->enterExprStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_For) {
			return $this->visitor->enterForStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Foreach) {
			return $this->visitor->enterForeachStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Function) {
			return $this->visitor->enterFunctionStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Global) {
			return $this->visitor->enterGlobalStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Goto) {
			return $this->visitor->enterGotoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_HaltCompiler) {
			return $this->visitor->enterHaltCompilerStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_If) {
			return $this->visitor->enterIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_InlineHTML) {
			return $this->visitor->enterInlineHTMLStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
			return $this->visitor->enterInterfaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Label) {
			return $this->visitor->enterLabelStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Namespace) {
			return $this->visitor->enterNamespaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Property) {
			return $this->visitor->enterPropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_PropertyProperty) {
			return $this->visitor->enterPropertyPropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Return) {
			return $this->visitor->enterReturnStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Static) {
			return $this->visitor->enterStaticStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_StaticVar) {
			return $this->visitor->enterStaticVarStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Switch) {
			return $this->visitor->enterSwitchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Throw) {
			return $this->visitor->enterThrowStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Trait) {
			return $this->visitor->enterTraitStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUse) {
			return $this->visitor->enterTraitUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Alias) {
			return $this->visitor->enterAliasTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Precedence) {
			return $this->visitor->enterPrecedenceTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation) {
			return $this->visitor->enterTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TryCatch) {
			return $this->visitor->enterTryCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Unset) {
			return $this->visitor->enterUnsetStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Use) {
			return $this->visitor->enterUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_UseUse) {
			return $this->visitor->enterUseUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_While) {
			return $this->visitor->enterWhileStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt) {
			return $this->visitor->enterStmt($node);
		} elseif ($node instanceof PHPParser_Node_Expr) {
			return $this->visitor->enterExpr($node);
		}
	}
	public function leaveNode(PHPParser_Node $node)
	{
		if ($node instanceof PHPParser_Node_Arg) {
			return $this->visitor->leaveArg($node);
		} elseif ($node instanceof PHPParser_Node_Const) {
			return $this->visitor->leaveConst($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Array) {
			return $this->visitor->leaveArrayExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayDimFetch) {
			return $this->visitor->leaveArrayDimFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ArrayItem) {
			return $this->visitor->leaveArrayItemExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Assign) {
			return $this->visitor->leaveAssignExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseAnd) {
			return $this->visitor->leaveAssignBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseOr) {
			return $this->visitor->leaveAssignBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignBitwiseXor) {
			return $this->visitor->leaveAssignBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignConcat) {
			return $this->visitor->leaveAssignConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignDiv) {
			return $this->visitor->leaveAssignDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMinus) {
			return $this->visitor->leaveAssignMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMod) {
			return $this->visitor->leaveAssignModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignMul) {
			return $this->visitor->leaveAssignMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignPlus) {
			return $this->visitor->leaveAssignPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignRef) {
			return $this->visitor->leaveAssignRefExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftLeft) {
			return $this->visitor->leaveAssignShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_AssignShiftRight) {
			return $this->visitor->leaveAssignShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseAnd) {
			return $this->visitor->leaveBitwiseAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseNot) {
			return $this->visitor->leaveBitwiseNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseOr) {
			return $this->visitor->leaveBitwiseOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BitwiseXor) {
			return $this->visitor->leaveBitwiseXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanAnd) {
			return $this->visitor->leaveBooleanAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanNot) {
			return $this->visitor->leaveBooleanNotExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_BooleanOr) {
			return $this->visitor->leaveBooleanOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Array) {
			return $this->visitor->leaveArrayCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Bool) {
			return $this->visitor->leaveBoolCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Double) {
			return $this->visitor->leaveDoubleCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Int) {
			return $this->visitor->leaveIntCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Object) {
			return $this->visitor->leaveObjectCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_String) {
			return $this->visitor->leaveStringCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast_Unset) {
			return $this->visitor->leaveUnsetCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Cast) {
			return $this->visitor->leaveCastExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClassConstFetch) {
			return $this->visitor->leaveClassConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Clone) {
			return $this->visitor->leaveCloneExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Closure) {
			return $this->visitor->leaveClosureExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ClosureUse) {
			return $this->visitor->leaveClosureUseExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Concat) {
			return $this->visitor->leaveConcatExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ConstFetch) {
			return $this->visitor->leaveConstFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Div) {
			return $this->visitor->leaveDivExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Empty) {
			return $this->visitor->leaveEmptyExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Equal) {
			return $this->visitor->leaveEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ErrorSuppress) {
			return $this->visitor->leaveErrorSuppressExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Eval) {
			return $this->visitor->leaveEvalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Exit) {
			return $this->visitor->leaveExitExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_FuncCall) {
			return $this->visitor->leaveFuncCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Greater) {
			return $this->visitor->leaveGreaterExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_GreaterOrEqual) {
			return $this->visitor->leaveGreaterOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Identical) {
			return $this->visitor->leaveIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Include) {
			return $this->visitor->leaveIncludeExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Instanceof) {
			return $this->visitor->leaveInstanceofExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Isset) {
			return $this->visitor->leaveIssetExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_List) {
			return $this->visitor->leaveListExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalAnd) {
			return $this->visitor->leaveLogicalAndExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalOr) {
			return $this->visitor->leaveLogicalOrExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_LogicalXor) {
			return $this->visitor->leaveLogicalXorExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_MethodCall) {
			return $this->visitor->leaveMethodCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Minus) {
			return $this->visitor->leaveMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mod) {
			return $this->visitor->leaveModExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Mul) {
			return $this->visitor->leaveMulExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_New) {
			return $this->visitor->leaveNewExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotEqual) {
			return $this->visitor->leaveNotEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_NotIdentical) {
			return $this->visitor->leaveNotIdenticalExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Plus) {
			return $this->visitor->leavePlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostDec) {
			return $this->visitor->leavePostDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PostInc) {
			return $this->visitor->leavePostIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreDec) {
			return $this->visitor->leavePreDecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PreInc) {
			return $this->visitor->leavePreIncExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Print) {
			return $this->visitor->leavePrintExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_PropertyFetch) {
			return $this->visitor->leavePropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShellExec) {
			return $this->visitor->leaveShellExecExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftLeft) {
			return $this->visitor->leaveShiftLeftExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_ShiftRight) {
			return $this->visitor->leaveShiftRightExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Smaller) {
			return $this->visitor->leaveSmallerExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_SmallerOrEqual) {
			return $this->visitor->leaveSmallerOrEqualExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticCall) {
			return $this->visitor->leaveStaticCallExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_StaticPropertyFetch) {
			return $this->visitor->leaveStaticPropertyFetchExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Ternary) {
			return $this->visitor->leaveTernaryExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryMinus) {
			return $this->visitor->leaveUnaryMinusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_UnaryPlus) {
			return $this->visitor->leaveUnaryPlusExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Variable) {
			return $this->visitor->leaveVariableExpr($node);
		} elseif ($node instanceof PHPParser_Node_Expr_Yield) {
			return $this->visitor->leaveYieldExpr($node);
		} elseif ($node instanceof PHPParser_Node_Name_FullyQualified) {
			return $this->visitor->leaveFullyQualifiedName($node);
		} elseif ($node instanceof PHPParser_Node_Name_Relative) {
			return $this->visitor->leaveRelativeName($node);
		} elseif ($node instanceof PHPParser_Node_Name) {
			return $this->visitor->leaveName($node);
		} elseif ($node instanceof PHPParser_Node_Param) {
			return $this->visitor->leaveParam($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_ClassConst) {
			return $this->visitor->leaveClassConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DirConst) {
			return $this->visitor->leaveDirConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_DNumber) {
			return $this->visitor->leaveDNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_Encapsed) {
			return $this->visitor->leaveEncapsedScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FileConst) {
			return $this->visitor->leaveFileConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_FuncConst) {
			return $this->visitor->leaveFuncConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LineConst) {
			return $this->visitor->leaveLineConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_LNumber) {
			return $this->visitor->leaveLNumberScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_MethodConst) {
			return $this->visitor->leaveMethodConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_NSConst) {
			return $this->visitor->leaveNSConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_String) {
			return $this->visitor->leaveStringScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar_TraitConst) {
			return $this->visitor->leaveTraitConstScalar($node);
		} elseif ($node instanceof PHPParser_Node_Scalar) {
			return $this->visitor->leaveScalar($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Break) {
			return $this->visitor->leaveBreakStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Case) {
			return $this->visitor->leaveCaseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Catch) {
			return $this->visitor->leaveCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Class) {
			return $this->visitor->leaveClassStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassConst) {
			return $this->visitor->leaveClassConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ClassMethod) {
			return $this->visitor->leaveClassMethodStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Const) {
			return $this->visitor->leaveConstStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Continue) {
			return $this->visitor->leaveContinueStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Declare) {
			return $this->visitor->leaveDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_DeclareDeclare) {
			return $this->visitor->leaveDeclareDeclareStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Do) {
			return $this->visitor->leaveDoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Echo) {
			return $this->visitor->leaveEchoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Else) {
			return $this->visitor->leaveElseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_ElseIf) {
			return $this->visitor->leaveElseIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Expr) {
			return $this->visitor->leaveExprStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_For) {
			return $this->visitor->leaveForStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Foreach) {
			return $this->visitor->leaveForeachStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Function) {
			return $this->visitor->leaveFunctionStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Global) {
			return $this->visitor->leaveGlobalStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Goto) {
			return $this->visitor->leaveGotoStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_HaltCompiler) {
			return $this->visitor->leaveHaltCompilerStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_If) {
			return $this->visitor->leaveIfStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_InlineHTML) {
			return $this->visitor->leaveInlineHTMLStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
			return $this->visitor->leaveInterfaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Label) {
			return $this->visitor->leaveLabelStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Namespace) {
			return $this->visitor->leaveNamespaceStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Property) {
			return $this->visitor->leavePropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_PropertyProperty) {
			return $this->visitor->leavePropertyPropertyStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Return) {
			return $this->visitor->leaveReturnStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Static) {
			return $this->visitor->leaveStaticStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_StaticVar) {
			return $this->visitor->leaveStaticVarStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Switch) {
			return $this->visitor->leaveSwitchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Throw) {
			return $this->visitor->leaveThrowStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Trait) {
			return $this->visitor->leaveTraitStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUse) {
			return $this->visitor->leaveTraitUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Alias) {
			return $this->visitor->leaveAliasTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation_Precedence) {
			return $this->visitor->leavePrecedenceTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TraitUseAdaptation) {
			return $this->visitor->leaveTraitUseAdaptationStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_TryCatch) {
			return $this->visitor->leaveTryCatchStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Unset) {
			return $this->visitor->leaveUnsetStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_Use) {
			return $this->visitor->leaveUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_UseUse) {
			return $this->visitor->leaveUseUseStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt_While) {
			return $this->visitor->leaveWhileStmt($node);
		} elseif ($node instanceof PHPParser_Node_Stmt) {
			return $this->visitor->leaveStmt($node);
		} elseif ($node instanceof PHPParser_Node_Expr) {
			return $this->visitor->leaveExpr($node);
		}
	}
}
?>