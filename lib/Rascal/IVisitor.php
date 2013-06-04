<?php
interface IVisitor
{
	public function enterArg(PHPParser_Node_Arg $node);
	public function enterConst(PHPParser_Node_Const $node);
	public function enterArrayExpr(PHPParser_Node_Expr_Array $node);
	public function enterArrayDimFetchExpr(PHPParser_Node_Expr_ArrayDimFetch $node);
	public function enterArrayItemExpr(PHPParser_Node_Expr_ArrayItem $node);
	public function enterAssignExpr(PHPParser_Node_Expr_Assign $node);
	public function enterAssignBitwiseAndExpr(PHPParser_Node_Expr_AssignBitwiseAnd $node);
	public function enterAssignBitwiseOrExpr(PHPParser_Node_Expr_AssignBitwiseOr $node);
	public function enterAssignBitwiseXorExpr(PHPParser_Node_Expr_AssignBitwiseXor $node);
	public function enterAssignConcatExpr(PHPParser_Node_Expr_AssignConcat $node);
	public function enterAssignDivExpr(PHPParser_Node_Expr_AssignDiv $node);
	public function enterAssignMinusExpr(PHPParser_Node_Expr_AssignMinus $node);
	public function enterAssignModExpr(PHPParser_Node_Expr_AssignMod $node);
	public function enterAssignMulExpr(PHPParser_Node_Expr_AssignMul $node);
	public function enterAssignPlusExpr(PHPParser_Node_Expr_AssignPlus $node);
	public function enterAssignRefExpr(PHPParser_Node_Expr_AssignRef $node);
	public function enterAssignShiftLeftExpr(PHPParser_Node_Expr_AssignShiftLeft $node);
	public function enterAssignShiftRightExpr(PHPParser_Node_Expr_AssignShiftRight $node);
	public function enterBitwiseAndExpr(PHPParser_Node_Expr_BitwiseAnd $node);
	public function enterBitwiseNotExpr(PHPParser_Node_Expr_BitwiseNot $node);
	public function enterBitwiseOrExpr(PHPParser_Node_Expr_BitwiseOr $node);
	public function enterBitwiseXorExpr(PHPParser_Node_Expr_BitwiseXor $node);
	public function enterBooleanAndExpr(PHPParser_Node_Expr_BooleanAnd $node);
	public function enterBooleanNotExpr(PHPParser_Node_Expr_BooleanNot $node);
	public function enterBooleanOrExpr(PHPParser_Node_Expr_BooleanOr $node);
	public function enterArrayCastExpr(PHPParser_Node_Expr_Cast_Array $node);
	public function enterBoolCastExpr(PHPParser_Node_Expr_Cast_Bool $node);
	public function enterDoubleCastExpr(PHPParser_Node_Expr_Cast_Double $node);
	public function enterIntCastExpr(PHPParser_Node_Expr_Cast_Int $node);
	public function enterObjectCastExpr(PHPParser_Node_Expr_Cast_Object $node);
	public function enterStringCastExpr(PHPParser_Node_Expr_Cast_String $node);
	public function enterUnsetCastExpr(PHPParser_Node_Expr_Cast_Unset $node);
	public function enterCastExpr(PHPParser_Node_Expr_Cast $node);
	public function enterClassConstFetchExpr(PHPParser_Node_Expr_ClassConstFetch $node);
	public function enterCloneExpr(PHPParser_Node_Expr_Clone $node);
	public function enterClosureExpr(PHPParser_Node_Expr_Closure $node);
	public function enterClosureUseExpr(PHPParser_Node_Expr_ClosureUse $node);
	public function enterConcatExpr(PHPParser_Node_Expr_Concat $node);
	public function enterConstFetchExpr(PHPParser_Node_Expr_ConstFetch $node);
	public function enterDivExpr(PHPParser_Node_Expr_Div $node);
	public function enterEmptyExpr(PHPParser_Node_Expr_Empty $node);
	public function enterEqualExpr(PHPParser_Node_Expr_Equal $node);
	public function enterErrorSuppressExpr(PHPParser_Node_Expr_ErrorSuppress $node);
	public function enterEvalExpr(PHPParser_Node_Expr_Eval $node);
	public function enterExitExpr(PHPParser_Node_Expr_Exit $node);
	public function enterFuncCallExpr(PHPParser_Node_Expr_FuncCall $node);
	public function enterGreaterExpr(PHPParser_Node_Expr_Greater $node);
	public function enterGreaterOrEqualExpr(PHPParser_Node_Expr_GreaterOrEqual $node);
	public function enterIdenticalExpr(PHPParser_Node_Expr_Identical $node);
	public function enterIncludeExpr(PHPParser_Node_Expr_Include $node);
	public function enterInstanceofExpr(PHPParser_Node_Expr_Instanceof $node);
	public function enterIssetExpr(PHPParser_Node_Expr_Isset $node);
	public function enterListExpr(PHPParser_Node_Expr_List $node);
	public function enterLogicalAndExpr(PHPParser_Node_Expr_LogicalAnd $node);
	public function enterLogicalOrExpr(PHPParser_Node_Expr_LogicalOr $node);
	public function enterLogicalXorExpr(PHPParser_Node_Expr_LogicalXor $node);
	public function enterMethodCallExpr(PHPParser_Node_Expr_MethodCall $node);
	public function enterMinusExpr(PHPParser_Node_Expr_Minus $node);
	public function enterModExpr(PHPParser_Node_Expr_Mod $node);
	public function enterMulExpr(PHPParser_Node_Expr_Mul $node);
	public function enterNewExpr(PHPParser_Node_Expr_New $node);
	public function enterNotEqualExpr(PHPParser_Node_Expr_NotEqual $node);
	public function enterNotIdenticalExpr(PHPParser_Node_Expr_NotIdentical $node);
	public function enterPlusExpr(PHPParser_Node_Expr_Plus $node);
	public function enterPostDecExpr(PHPParser_Node_Expr_PostDec $node);
	public function enterPostIncExpr(PHPParser_Node_Expr_PostInc $node);
	public function enterPreDecExpr(PHPParser_Node_Expr_PreDec $node);
	public function enterPreIncExpr(PHPParser_Node_Expr_PreInc $node);
	public function enterPrintExpr(PHPParser_Node_Expr_Print $node);
	public function enterPropertyFetchExpr(PHPParser_Node_Expr_PropertyFetch $node);
	public function enterShellExecExpr(PHPParser_Node_Expr_ShellExec $node);
	public function enterShiftLeftExpr(PHPParser_Node_Expr_ShiftLeft $node);
	public function enterShiftRightExpr(PHPParser_Node_Expr_ShiftRight $node);
	public function enterSmallerExpr(PHPParser_Node_Expr_Smaller $node);
	public function enterSmallerOrEqualExpr(PHPParser_Node_Expr_SmallerOrEqual $node);
	public function enterStaticCallExpr(PHPParser_Node_Expr_StaticCall $node);
	public function enterStaticPropertyFetchExpr(PHPParser_Node_Expr_StaticPropertyFetch $node);
	public function enterTernaryExpr(PHPParser_Node_Expr_Ternary $node);
	public function enterUnaryMinusExpr(PHPParser_Node_Expr_UnaryMinus $node);
	public function enterUnaryPlusExpr(PHPParser_Node_Expr_UnaryPlus $node);
	public function enterVariableExpr(PHPParser_Node_Expr_Variable $node);
	public function enterYieldExpr(PHPParser_Node_Expr_Yield $node);
	public function enterFullyQualifiedName(PHPParser_Node_Name_FullyQualified $node);
	public function enterRelativeName(PHPParser_Node_Name_Relative $node);
	public function enterName(PHPParser_Node_Name $node);
	public function enterParam(PHPParser_Node_Param $node);
	public function enterClassConstScalar(PHPParser_Node_Scalar_ClassConst $node);
	public function enterDirConstScalar(PHPParser_Node_Scalar_DirConst $node);
	public function enterDNumberScalar(PHPParser_Node_Scalar_DNumber $node);
	public function enterEncapsedScalar(PHPParser_Node_Scalar_Encapsed $node);
	public function enterFileConstScalar(PHPParser_Node_Scalar_FileConst $node);
	public function enterFuncConstScalar(PHPParser_Node_Scalar_FuncConst $node);
	public function enterLineConstScalar(PHPParser_Node_Scalar_LineConst $node);
	public function enterLNumberScalar(PHPParser_Node_Scalar_LNumber $node);
	public function enterMethodConstScalar(PHPParser_Node_Scalar_MethodConst $node);
	public function enterNSConstScalar(PHPParser_Node_Scalar_NSConst $node);
	public function enterStringScalar(PHPParser_Node_Scalar_String $node);
	public function enterTraitConstScalar(PHPParser_Node_Scalar_TraitConst $node);
	public function enterScalar(PHPParser_Node_Scalar $node);
	public function enterBreakStmt(PHPParser_Node_Stmt_Break $node);
	public function enterCaseStmt(PHPParser_Node_Stmt_Case $node);
	public function enterCatchStmt(PHPParser_Node_Stmt_Catch $node);
	public function enterClassStmt(PHPParser_Node_Stmt_Class $node);
	public function enterClassConstStmt(PHPParser_Node_Stmt_ClassConst $node);
	public function enterClassMethodStmt(PHPParser_Node_Stmt_ClassMethod $node);
	public function enterConstStmt(PHPParser_Node_Stmt_Const $node);
	public function enterContinueStmt(PHPParser_Node_Stmt_Continue $node);
	public function enterDeclareStmt(PHPParser_Node_Stmt_Declare $node);
	public function enterDeclareDeclareStmt(PHPParser_Node_Stmt_DeclareDeclare $node);
	public function enterDoStmt(PHPParser_Node_Stmt_Do $node);
	public function enterEchoStmt(PHPParser_Node_Stmt_Echo $node);
	public function enterElseStmt(PHPParser_Node_Stmt_Else $node);
	public function enterElseIfStmt(PHPParser_Node_Stmt_ElseIf $node);
	public function enterExprStmt(PHPParser_Node_Stmt_Expr $node);
	public function enterForStmt(PHPParser_Node_Stmt_For $node);
	public function enterForeachStmt(PHPParser_Node_Stmt_Foreach $node);
	public function enterFunctionStmt(PHPParser_Node_Stmt_Function $node);
	public function enterGlobalStmt(PHPParser_Node_Stmt_Global $node);
	public function enterGotoStmt(PHPParser_Node_Stmt_Goto $node);
	public function enterHaltCompilerStmt(PHPParser_Node_Stmt_HaltCompiler $node);
	public function enterIfStmt(PHPParser_Node_Stmt_If $node);
	public function enterInlineHTMLStmt(PHPParser_Node_Stmt_InlineHTML $node);
	public function enterInterfaceStmt(PHPParser_Node_Stmt_Interface $node);
	public function enterLabelStmt(PHPParser_Node_Stmt_Label $node);
	public function enterNamespaceStmt(PHPParser_Node_Stmt_Namespace $node);
	public function enterPropertyStmt(PHPParser_Node_Stmt_Property $node);
	public function enterPropertyPropertyStmt(PHPParser_Node_Stmt_PropertyProperty $node);
	public function enterReturnStmt(PHPParser_Node_Stmt_Return $node);
	public function enterStaticStmt(PHPParser_Node_Stmt_Static $node);
	public function enterStaticVarStmt(PHPParser_Node_Stmt_StaticVar $node);
	public function enterSwitchStmt(PHPParser_Node_Stmt_Switch $node);
	public function enterThrowStmt(PHPParser_Node_Stmt_Throw $node);
	public function enterTraitStmt(PHPParser_Node_Stmt_Trait $node);
	public function enterTraitUseStmt(PHPParser_Node_Stmt_TraitUse $node);
	public function enterAliasTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Alias $node);
	public function enterPrecedenceTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Precedence $node);
	public function enterTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation $node);
	public function enterTryCatchStmt(PHPParser_Node_Stmt_TryCatch $node);
	public function enterUnsetStmt(PHPParser_Node_Stmt_Unset $node);
	public function enterUseStmt(PHPParser_Node_Stmt_Use $node);
	public function enterUseUseStmt(PHPParser_Node_Stmt_UseUse $node);
	public function enterWhileStmt(PHPParser_Node_Stmt_While $node);
	public function enterStmt(PHPParser_Node_Stmt $node);
	public function enterExpr(PHPParser_Node_Expr $node);

	public function leaveArg(PHPParser_Node_Arg $node);
	public function leaveConst(PHPParser_Node_Const $node);
	public function leaveArrayExpr(PHPParser_Node_Expr_Array $node);
	public function leaveArrayDimFetchExpr(PHPParser_Node_Expr_ArrayDimFetch $node);
	public function leaveArrayItemExpr(PHPParser_Node_Expr_ArrayItem $node);
	public function leaveAssignExpr(PHPParser_Node_Expr_Assign $node);
	public function leaveAssignBitwiseAndExpr(PHPParser_Node_Expr_AssignBitwiseAnd $node);
	public function leaveAssignBitwiseOrExpr(PHPParser_Node_Expr_AssignBitwiseOr $node);
	public function leaveAssignBitwiseXorExpr(PHPParser_Node_Expr_AssignBitwiseXor $node);
	public function leaveAssignConcatExpr(PHPParser_Node_Expr_AssignConcat $node);
	public function leaveAssignDivExpr(PHPParser_Node_Expr_AssignDiv $node);
	public function leaveAssignMinusExpr(PHPParser_Node_Expr_AssignMinus $node);
	public function leaveAssignModExpr(PHPParser_Node_Expr_AssignMod $node);
	public function leaveAssignMulExpr(PHPParser_Node_Expr_AssignMul $node);
	public function leaveAssignPlusExpr(PHPParser_Node_Expr_AssignPlus $node);
	public function leaveAssignRefExpr(PHPParser_Node_Expr_AssignRef $node);
	public function leaveAssignShiftLeftExpr(PHPParser_Node_Expr_AssignShiftLeft $node);
	public function leaveAssignShiftRightExpr(PHPParser_Node_Expr_AssignShiftRight $node);
	public function leaveBitwiseAndExpr(PHPParser_Node_Expr_BitwiseAnd $node);
	public function leaveBitwiseNotExpr(PHPParser_Node_Expr_BitwiseNot $node);
	public function leaveBitwiseOrExpr(PHPParser_Node_Expr_BitwiseOr $node);
	public function leaveBitwiseXorExpr(PHPParser_Node_Expr_BitwiseXor $node);
	public function leaveBooleanAndExpr(PHPParser_Node_Expr_BooleanAnd $node);
	public function leaveBooleanNotExpr(PHPParser_Node_Expr_BooleanNot $node);
	public function leaveBooleanOrExpr(PHPParser_Node_Expr_BooleanOr $node);
	public function leaveArrayCastExpr(PHPParser_Node_Expr_Cast_Array $node);
	public function leaveBoolCastExpr(PHPParser_Node_Expr_Cast_Bool $node);
	public function leaveDoubleCastExpr(PHPParser_Node_Expr_Cast_Double $node);
	public function leaveIntCastExpr(PHPParser_Node_Expr_Cast_Int $node);
	public function leaveObjectCastExpr(PHPParser_Node_Expr_Cast_Object $node);
	public function leaveStringCastExpr(PHPParser_Node_Expr_Cast_String $node);
	public function leaveUnsetCastExpr(PHPParser_Node_Expr_Cast_Unset $node);
	public function leaveCastExpr(PHPParser_Node_Expr_Cast $node);
	public function leaveClassConstFetchExpr(PHPParser_Node_Expr_ClassConstFetch $node);
	public function leaveCloneExpr(PHPParser_Node_Expr_Clone $node);
	public function leaveClosureExpr(PHPParser_Node_Expr_Closure $node);
	public function leaveClosureUseExpr(PHPParser_Node_Expr_ClosureUse $node);
	public function leaveConcatExpr(PHPParser_Node_Expr_Concat $node);
	public function leaveConstFetchExpr(PHPParser_Node_Expr_ConstFetch $node);
	public function leaveDivExpr(PHPParser_Node_Expr_Div $node);
	public function leaveEmptyExpr(PHPParser_Node_Expr_Empty $node);
	public function leaveEqualExpr(PHPParser_Node_Expr_Equal $node);
	public function leaveErrorSuppressExpr(PHPParser_Node_Expr_ErrorSuppress $node);
	public function leaveEvalExpr(PHPParser_Node_Expr_Eval $node);
	public function leaveExitExpr(PHPParser_Node_Expr_Exit $node);
	public function leaveFuncCallExpr(PHPParser_Node_Expr_FuncCall $node);
	public function leaveGreaterExpr(PHPParser_Node_Expr_Greater $node);
	public function leaveGreaterOrEqualExpr(PHPParser_Node_Expr_GreaterOrEqual $node);
	public function leaveIdenticalExpr(PHPParser_Node_Expr_Identical $node);
	public function leaveIncludeExpr(PHPParser_Node_Expr_Include $node);
	public function leaveInstanceofExpr(PHPParser_Node_Expr_Instanceof $node);
	public function leaveIssetExpr(PHPParser_Node_Expr_Isset $node);
	public function leaveListExpr(PHPParser_Node_Expr_List $node);
	public function leaveLogicalAndExpr(PHPParser_Node_Expr_LogicalAnd $node);
	public function leaveLogicalOrExpr(PHPParser_Node_Expr_LogicalOr $node);
	public function leaveLogicalXorExpr(PHPParser_Node_Expr_LogicalXor $node);
	public function leaveMethodCallExpr(PHPParser_Node_Expr_MethodCall $node);
	public function leaveMinusExpr(PHPParser_Node_Expr_Minus $node);
	public function leaveModExpr(PHPParser_Node_Expr_Mod $node);
	public function leaveMulExpr(PHPParser_Node_Expr_Mul $node);
	public function leaveNewExpr(PHPParser_Node_Expr_New $node);
	public function leaveNotEqualExpr(PHPParser_Node_Expr_NotEqual $node);
	public function leaveNotIdenticalExpr(PHPParser_Node_Expr_NotIdentical $node);
	public function leavePlusExpr(PHPParser_Node_Expr_Plus $node);
	public function leavePostDecExpr(PHPParser_Node_Expr_PostDec $node);
	public function leavePostIncExpr(PHPParser_Node_Expr_PostInc $node);
	public function leavePreDecExpr(PHPParser_Node_Expr_PreDec $node);
	public function leavePreIncExpr(PHPParser_Node_Expr_PreInc $node);
	public function leavePrintExpr(PHPParser_Node_Expr_Print $node);
	public function leavePropertyFetchExpr(PHPParser_Node_Expr_PropertyFetch $node);
	public function leaveShellExecExpr(PHPParser_Node_Expr_ShellExec $node);
	public function leaveShiftLeftExpr(PHPParser_Node_Expr_ShiftLeft $node);
	public function leaveShiftRightExpr(PHPParser_Node_Expr_ShiftRight $node);
	public function leaveSmallerExpr(PHPParser_Node_Expr_Smaller $node);
	public function leaveSmallerOrEqualExpr(PHPParser_Node_Expr_SmallerOrEqual $node);
	public function leaveStaticCallExpr(PHPParser_Node_Expr_StaticCall $node);
	public function leaveStaticPropertyFetchExpr(PHPParser_Node_Expr_StaticPropertyFetch $node);
	public function leaveTernaryExpr(PHPParser_Node_Expr_Ternary $node);
	public function leaveUnaryMinusExpr(PHPParser_Node_Expr_UnaryMinus $node);
	public function leaveUnaryPlusExpr(PHPParser_Node_Expr_UnaryPlus $node);
	public function leaveVariableExpr(PHPParser_Node_Expr_Variable $node);
	public function leaveYieldExpr(PHPParser_Node_Expr_Yield $node);
	public function leaveFullyQualifiedName(PHPParser_Node_Name_FullyQualified $node);
	public function leaveRelativeName(PHPParser_Node_Name_Relative $node);
	public function leaveName(PHPParser_Node_Name $node);
	public function leaveParam(PHPParser_Node_Param $node);
	public function leaveClassConstScalar(PHPParser_Node_Scalar_ClassConst $node);
	public function leaveDirConstScalar(PHPParser_Node_Scalar_DirConst $node);
	public function leaveDNumberScalar(PHPParser_Node_Scalar_DNumber $node);
	public function leaveEncapsedScalar(PHPParser_Node_Scalar_Encapsed $node);
	public function leaveFileConstScalar(PHPParser_Node_Scalar_FileConst $node);
	public function leaveFuncConstScalar(PHPParser_Node_Scalar_FuncConst $node);
	public function leaveLineConstScalar(PHPParser_Node_Scalar_LineConst $node);
	public function leaveLNumberScalar(PHPParser_Node_Scalar_LNumber $node);
	public function leaveMethodConstScalar(PHPParser_Node_Scalar_MethodConst $node);
	public function leaveNSConstScalar(PHPParser_Node_Scalar_NSConst $node);
	public function leaveStringScalar(PHPParser_Node_Scalar_String $node);
	public function leaveTraitConstScalar(PHPParser_Node_Scalar_TraitConst $node);
	public function leaveScalar(PHPParser_Node_Scalar $node);
	public function leaveBreakStmt(PHPParser_Node_Stmt_Break $node);
	public function leaveCaseStmt(PHPParser_Node_Stmt_Case $node);
	public function leaveCatchStmt(PHPParser_Node_Stmt_Catch $node);
	public function leaveClassStmt(PHPParser_Node_Stmt_Class $node);
	public function leaveClassConstStmt(PHPParser_Node_Stmt_ClassConst $node);
	public function leaveClassMethodStmt(PHPParser_Node_Stmt_ClassMethod $node);
	public function leaveConstStmt(PHPParser_Node_Stmt_Const $node);
	public function leaveContinueStmt(PHPParser_Node_Stmt_Continue $node);
	public function leaveDeclareStmt(PHPParser_Node_Stmt_Declare $node);
	public function leaveDeclareDeclareStmt(PHPParser_Node_Stmt_DeclareDeclare $node);
	public function leaveDoStmt(PHPParser_Node_Stmt_Do $node);
	public function leaveEchoStmt(PHPParser_Node_Stmt_Echo $node);
	public function leaveElseStmt(PHPParser_Node_Stmt_Else $node);
	public function leaveElseIfStmt(PHPParser_Node_Stmt_ElseIf $node);
	public function leaveExprStmt(PHPParser_Node_Stmt_Expr $node);
	public function leaveForStmt(PHPParser_Node_Stmt_For $node);
	public function leaveForeachStmt(PHPParser_Node_Stmt_Foreach $node);
	public function leaveFunctionStmt(PHPParser_Node_Stmt_Function $node);
	public function leaveGlobalStmt(PHPParser_Node_Stmt_Global $node);
	public function leaveGotoStmt(PHPParser_Node_Stmt_Goto $node);
	public function leaveHaltCompilerStmt(PHPParser_Node_Stmt_HaltCompiler $node);
	public function leaveIfStmt(PHPParser_Node_Stmt_If $node);
	public function leaveInlineHTMLStmt(PHPParser_Node_Stmt_InlineHTML $node);
	public function leaveInterfaceStmt(PHPParser_Node_Stmt_Interface $node);
	public function leaveLabelStmt(PHPParser_Node_Stmt_Label $node);
	public function leaveNamespaceStmt(PHPParser_Node_Stmt_Namespace $node);
	public function leavePropertyStmt(PHPParser_Node_Stmt_Property $node);
	public function leavePropertyPropertyStmt(PHPParser_Node_Stmt_PropertyProperty $node);
	public function leaveReturnStmt(PHPParser_Node_Stmt_Return $node);
	public function leaveStaticStmt(PHPParser_Node_Stmt_Static $node);
	public function leaveStaticVarStmt(PHPParser_Node_Stmt_StaticVar $node);
	public function leaveSwitchStmt(PHPParser_Node_Stmt_Switch $node);
	public function leaveThrowStmt(PHPParser_Node_Stmt_Throw $node);
	public function leaveTraitStmt(PHPParser_Node_Stmt_Trait $node);
	public function leaveTraitUseStmt(PHPParser_Node_Stmt_TraitUse $node);
	public function leaveAliasTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Alias $node);
	public function leavePrecedenceTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Precedence $node);
	public function leaveTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation $node);
	public function leaveTryCatchStmt(PHPParser_Node_Stmt_TryCatch $node);
	public function leaveUnsetStmt(PHPParser_Node_Stmt_Unset $node);
	public function leaveUseStmt(PHPParser_Node_Stmt_Use $node);
	public function leaveUseUseStmt(PHPParser_Node_Stmt_UseUse $node);
	public function leaveWhileStmt(PHPParser_Node_Stmt_While $node);
	public function leaveStmt(PHPParser_Node_Stmt $node);
	public function leaveExpr(PHPParser_Node_Expr $node);

}
?>