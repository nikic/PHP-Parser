<?php
require '../PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

require_once 'UsefulVisitor.php';
require_once 'IVisitor.php';
require_once 'BaseVisitor.php';

class AST2Rascal extends BaseVisitor {
  private $filename = "";
  private $fragments = array();

  public function AST2Rascal($str)
  {
    $this->filename = $str;
  }

  private function rascalizeString($str) 
  {
    $newstr = "";
    foreach(str_split($str) as $char) {
      if ("<" == $char)
	$newstr .= "\\<";
      elseif (">" == $char)
	$newstr .= "\\>";
      elseif ("'" == $char)
	$newstr .= "\\'";
      elseif ("\n" == $char)
	$newstr .= "\\n";
      elseif ("\\" == $char)
	$newstr .= "\\\\";
      elseif ("\"" == $char)
	$newstr .= "\\\"";
      else
	$newstr .= $char;
    }
    return $newstr;
  }

  private function rascalizeStringLiteral($str) 
  {
    $newstr = "";
    foreach(str_split($str) as $char) {
      if ("<" == $char)
	$newstr .= "\\<";
      elseif (">" == $char)
	$newstr .= "\\>";
      elseif ("'" == $char)
	$newstr .= "\\'";
      elseif ("\n" == $char)
	$newstr .= "\\n";
      elseif ("\\" == $char)
	$newstr .= "\\\\";
      elseif ("\"" == $char)
	$newstr .= "\\\"";
      else
	$newstr .= $char;
    }
    return $newstr;
  }

  private function tagWithLine(PHPParser_Node $node)
  {
    //return "[@at=|file://{$this->filename}|(0,0,<{$node->getLine()},0>,<{$node->getLine()},0>)]";
    return "";
  }

  public function leaveArg(PHPParser_Node_Arg $node)
  {
    $fragment = "actualParameter(";
    $fragment .= array_pop($this->fragments);
    $fragment .= ",";
    if ($node->byRef)
      $fragment .= "true";
    else
      $fragment .= "false";
    $fragment .= ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveConst(PHPParser_Node_Const $node)
  {
    $fragment = "const(\"" . $this->rascalizeString($node->name) . "\",";
    $fragment .= array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveArrayExpr(PHPParser_Node_Expr_Array $node)
  {
    $fragment = "array([";
    $items = array();
    foreach($node->items as $item)
      $items[] = array_pop($this->fragments);
    $fragment .= implode(",",array_reverse($items));
    $fragment .= "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveArrayDimFetchExpr(PHPParser_Node_Expr_ArrayDimFetch $node)
  {
    $dim = "noExpr()";
    if (null != $node->dim)
      $dim = "someExpr(" . array_pop($this->fragments) . ")";
    $fragment = "fetchArrayDim(" . array_pop($this->fragments) . "," . $dim . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveArrayItemExpr(PHPParser_Node_Expr_ArrayItem $node)
  {
    $nodeValue = array_pop($this->fragments);
    
    if (null == $node->key)
      $key = "noExpr()";
    else
      $key = "someExpr(" . array_pop($this->fragments) . ")";
    
    if ($node->byRef)
      $byRef = "true";
    else
      $byRef = "false";

    $fragment = "arrayElement(" . $key . "," . $nodeValue . "," . $byRef . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveAssignExpr(PHPParser_Node_Expr_Assign $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assign(".$assignVar.",".$assignExpr.")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveAssignBitwiseAndExpr(PHPParser_Node_Expr_AssignBitwiseAnd $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseAnd())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignBitwiseOrExpr(PHPParser_Node_Expr_AssignBitwiseOr $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseOr())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignBitwiseXorExpr(PHPParser_Node_Expr_AssignBitwiseXor $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseXor())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignConcatExpr(PHPParser_Node_Expr_AssignConcat $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",concat())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignDivExpr(PHPParser_Node_Expr_AssignDiv $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",div())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignListExpr(PHPParser_Node_Expr_AssignList $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVars = array();
    // TODO: Need to verify this, not sure if these are all fragments
    foreach($node->vars as $var) {
      if (null != $var) {
	$assignVars[] = "someExpr(" . array_pop($this->fragments) . ")";
      } else {
	$assignVars[] = "noExpr()";
      }
    }

    $fragment = "listAssign([" . implode(",",array_reverse($assignVars)) . "]," . $assignExpr . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignMinusExpr(PHPParser_Node_Expr_AssignMinus $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",minus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignModExpr(PHPParser_Node_Expr_AssignMod $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",\\mod())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignMulExpr(PHPParser_Node_Expr_AssignMul $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",mul())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignPlusExpr(PHPParser_Node_Expr_AssignPlus $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",plus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignRefExpr(PHPParser_Node_Expr_AssignRef $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "refAssign(".$assignVar.",".$assignExpr.")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignShiftLeftExpr(PHPParser_Node_Expr_AssignShiftLeft $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",leftShift())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveAssignShiftRightExpr(PHPParser_Node_Expr_AssignShiftRight $node)
  {
    $assignExpr = array_pop($this->fragments);
    $assignVar = array_pop($this->fragments);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",rightShift())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveBitwiseAndExpr(PHPParser_Node_Expr_BitwiseAnd $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseAnd())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveBitwiseNotExpr(PHPParser_Node_Expr_BitwiseNot $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",bitwiseNot())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
  
  public function leaveBitwiseOrExpr(PHPParser_Node_Expr_BitwiseOr $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseOr())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveBitwiseXorExpr(PHPParser_Node_Expr_BitwiseXor $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseXor())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveBooleanAndExpr(PHPParser_Node_Expr_BooleanAnd $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",booleanAnd())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveBooleanNotExpr(PHPParser_Node_Expr_BooleanNot $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",booleanNot())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveBooleanOrExpr(PHPParser_Node_Expr_BooleanOr $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",booleanOr())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveArrayCastExpr(PHPParser_Node_Expr_Cast_Array $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(array()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveBoolCastExpr(PHPParser_Node_Expr_Cast_Bool $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(\bool()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveDoubleCastExpr(PHPParser_Node_Expr_Cast_Double $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(float()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveIntCastExpr(PHPParser_Node_Expr_Cast_Int $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(\int()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveObjectCastExpr(PHPParser_Node_Expr_Cast_Object $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(object()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveStringCastExpr(PHPParser_Node_Expr_Cast_String $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(string()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveUnsetCastExpr(PHPParser_Node_Expr_Cast_Unset $node)
  {
    $toCast = array_pop($this->fragments);
    $fragment = "cast(unset()," . $toCast . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveClassConstFetchExpr(PHPParser_Node_Expr_ClassConstFetch $node)
  {
    $name = array_pop($this->fragments);
    if ($node->class instanceof PHPParser_Node_Name)
      $name = "name({$name})";
    else
      $name = "expr({$name})";
    $fragment = "fetchClassConst(" . $name . ",\"" . $this->rascalizeString($node->name) . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveCloneExpr(PHPParser_Node_Expr_Clone $node)
  {
    $fragment = "clone(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveClosureExpr(PHPParser_Node_Expr_Closure $node)
  {
    $body = array();
    $params = array();
    $uses = array();

    foreach($node->uses as $use)
      $uses[] = array_pop($this->fragments);
    foreach($node->params as $param)
      $params[] = array_pop($this->fragments);
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);
    
    $fragment = "closure([" . implode(",",array_reverse($body)) . "],[";
    $fragment .= implode(",",array_reverse($params)) . "],[";
    $fragment .= implode(",",array_reverse($uses)) . "],";
    if ($node->byRef)
      $fragment .= "true,";
    else
      $fragment .= "false,";
    if ($node->static)
      $fragment .= "true";
    else
      $fragment .= "false";
    $fragment .= ")";
    
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveClosureUseExpr(PHPParser_Node_Expr_ClosureUse $node)
  {
    $fragment = "closureUse(\"" . $node->var . "\",";
    if ($node->byRef)
      $fragment .= "true";
    else
      $fragment .= "false";
    $fragment .= ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveConcatExpr(PHPParser_Node_Expr_Concat $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",concat())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveConstFetchExpr(PHPParser_Node_Expr_ConstFetch $node)
  {
    $fragment = "fetchConst(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveDivExpr(PHPParser_Node_Expr_Div $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",div())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveEmptyExpr(PHPParser_Node_Expr_Empty $node)
  {
    $fragment = "empty(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
  
  public function leaveEqualExpr(PHPParser_Node_Expr_Equal $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",equal())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveErrorSuppressExpr(PHPParser_Node_Expr_ErrorSuppress $node)
  {
    $fragment = "suppress(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveEvalExpr(PHPParser_Node_Expr_Eval $node)
  {
    $fragment = "eval(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveExitExpr(PHPParser_Node_Expr_Exit $node)
  {
    if (null != $node->expr)
      $fragment = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $fragment = "noExpr()";
    $fragment = "exit(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveFuncCallExpr(PHPParser_Node_Expr_FuncCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = array_pop($this->fragments);

    $name = array_pop($this->fragments);
    if ($node->name instanceof PHPParser_Node_Name)
      $name = "name({$name})";
    else
      $name = "expr({$name})";
    $fragment = "call(" . $name . ",[" . implode(",",array_reverse($args)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;

    return null;
  }

  public function leaveGreaterExpr(PHPParser_Node_Expr_Greater $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",gt())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveGreaterOrEqualExpr(PHPParser_Node_Expr_GreaterOrEqual $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",geq())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveIdenticalExpr(PHPParser_Node_Expr_Identical $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",identical())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveIncludeExpr(PHPParser_Node_Expr_Include $node)
  {
    $fragment = "include(" . array_pop($this->fragments) . ",";
    if (PHPParser_Node_Expr_Include::TYPE_INCLUDE == $node->type)
      $fragment .= "include()";
    elseif (PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE == $node->type)
      $fragment .= "includeOnce()";
    elseif (PHPParser_Node_Expr_Include::TYPE_REQUIRE == $node->type)
      $fragment .= "require()";
    else
      $fragment .= "requireOnce()";
    $fragment .= ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveInstanceofExpr(PHPParser_Node_Expr_Instanceof $node)
  {
    $right = array_pop($this->fragments);
    if ($node->class instanceOf PHPParser_Node_Name)
      $right = "name({$right})";
    else
      $right = "expr({$right})";

    $left = array_pop($this->fragments);

    $fragment = "instanceOf(".$left.",".$right.")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveIssetExpr(PHPParser_Node_Expr_Isset $node)
  {
    $fragment = "isSet(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveLogicalAndExpr(PHPParser_Node_Expr_LogicalAnd $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",logicalAnd())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveLogicalOrExpr(PHPParser_Node_Expr_LogicalOr $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",logicalOr())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
  	
  public function leaveLogicalXorExpr(PHPParser_Node_Expr_LogicalXor $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",logicalXor())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveMethodCallExpr(PHPParser_Node_Expr_MethodCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = array_pop($this->fragments);

    if ($node->name instanceof PHPParser_Node_Expr) {
      $name = array_pop($this->fragments);
      $name = "expr({$name})";
    } else {
      $name = "name(name(\"" . $node->name . "\"))";
    }

    $target = array_pop($this->fragments);

    $fragment = "methodCall(" . $target . "," . $name . ",[" . implode(",",array_reverse($args)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;

    return null;
  }

  public function leaveMinusExpr(PHPParser_Node_Expr_Minus $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",minus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveModExpr(PHPParser_Node_Expr_Mod $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",\\mod())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveMulExpr(PHPParser_Node_Expr_Mul $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",mul())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveNewExpr(PHPParser_Node_Expr_New $node)
  {
    $args = array();
    foreach ($node->args as $arg)
      $args[] = array_pop($this->fragments);

    $name = array_pop($this->fragments);

    if ($node->class instanceof PHPParser_Node_Expr)
      $name = "expr({$name})";
    else
      $name = "name({$name})";

    $fragment = "new(" . $name . ",[" . implode(",",array_reverse($args)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveNotEqualExpr(PHPParser_Node_Expr_NotEqual $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",notEqual())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveNotIdenticalExpr(PHPParser_Node_Expr_NotIdentical $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",notIdentical())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leavePlusExpr(PHPParser_Node_Expr_Plus $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",plus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leavePostDecExpr(PHPParser_Node_Expr_PostDec $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",postDec())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leavePostIncExpr(PHPParser_Node_Expr_PostInc $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",postInc())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leavePreDecExpr(PHPParser_Node_Expr_PreDec $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",preDec())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leavePreIncExpr(PHPParser_Node_Expr_PreInc $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",preInc())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leavePrintExpr(PHPParser_Node_Expr_Print $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "print(" . $operand . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leavePropertyFetchExpr(PHPParser_Node_Expr_PropertyFetch $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $fragment = "expr(" . array_pop($this->fragments) . ")";
    } else {
      $fragment = "name(name(\"" . $node->name . "\"))";
    }

    $fragment = "propertyFetch(" . array_pop($this->fragments) . "," . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveShellExecExpr(PHPParser_Node_Expr_ShellExec $node)
  {
    $parts = array();
    foreach($node->parts as $item) {
      if ($item instanceof PHPParser_Node_Expr) {
	$parts[] = array_pop($this->fragments);
      } else {
	$parts[] = "scalar(string(\"" . $this->rascalizeString($item) . "\"))";
      }
    }

    $fragment = "shellExec([" . implode(",",array_reverse($parts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveShiftLeftExpr(PHPParser_Node_Expr_ShiftLeft $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",leftShift())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveShiftRightExpr(PHPParser_Node_Expr_ShiftRight $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",rightShift())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveSmallerExpr(PHPParser_Node_Expr_Smaller $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",lt())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveSmallerOrEqualExpr(PHPParser_Node_Expr_SmallerOrEqual $node)
  {
    $right = array_pop($this->fragments);
    $left = array_pop($this->fragments);

    $fragment = "binaryOperation(".$left.",".$right.",leq())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveStaticCallExpr(PHPParser_Node_Expr_StaticCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = array_pop($this->fragments);

    if ($node->name instanceof PHPParser_Node_Expr)
      $name = "expr(" . array_pop($this->fragments) . ")";
    else
      $name = "name(name(\"" . $node->name . "\"))";

    if ($node->class instanceof PHPParser_Node_Expr) {
      $class = "expr(" . array_pop($this->fragments) . ")";
    } else {
      $class = "name(" . array_pop($this->fragments) . ")";
    }

    $fragment = "staticCall({$class},{$name},[" . implode(",",array_reverse($args)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveStaticPropertyFetchExpr(PHPParser_Node_Expr_StaticPropertyFetch $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $name = "expr(" . array_pop($this->fragments) . ")";
    } else {
      $name = "name(name(\"" . $node->name . "\"))";
    }

    if ($node->class instanceof PHPParser_Node_Expr) {
      $class = "expr(" . array_pop($this->fragments) . ")";
    } else {
      $class = "name(" . array_pop($this->fragments) . ")";
    }

    $fragment = "fetchStaticProperty({$class},{$name})";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveTernaryExpr(PHPParser_Node_Expr_Ternary $node)
  {
    $else = array_pop($this->fragments);
    if (null != $node->if)
      $if = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $if = "noExpr()";
    $cond = array_pop($this->fragments);
    
    $fragment = "ternary({$cond},{$if},{$else})";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveUnaryMinusExpr(PHPParser_Node_Expr_UnaryMinus $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",unaryMinus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveUnaryPlusExpr(PHPParser_Node_Expr_UnaryPlus $node)
  {
    $operand = array_pop($this->fragments);
    $fragment = "unaryOperation(".$operand.",unaryPlus())";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveVariableExpr(PHPParser_Node_Expr_Variable $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $fragment = "expr(" . array_pop($this->fragments) . ")";
    } else {
      $fragment = "name(name(\"" . $node->name . "\"))";
    }
    $fragment = "var(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveFullyQualifiedName(PHPParser_Node_Name_FullyQualified $node)
  {
    if (is_array($node->parts))
      $fragment = implode("::",$node->parts);
    else
      $fragment = $node->parts;
    $fragment = "name(\"" . $fragment . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveRelativeName(PHPParser_Node_Name_Relative $node)
  {
    if (is_array($node->parts))
      $fragment = implode("::",$node->parts);
    else
      $fragment = $node->parts;
    $fragment = "name(\"" . $fragment . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveName(PHPParser_Node_Name $node)
  {
    if (is_array($node->parts))
      $fragment = implode("::",$node->parts);
    else
      $fragment = $node->parts;
    $fragment = "name(\"" . $fragment . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveParam(PHPParser_Node_Param $node)
  {
    if (null == $node->type) {
      $type = "noName()";
    } else {
      if ($node->type instanceof PHPParser_Node_Name) {
	$type = "someName(" . array_pop($this->fragments) . ")";
      } else {
	$type = "someName(name(\"" . $node->type . "\"))";
      }
    }

    if (null == $node->default) {
      $default = "noExpr()";
    } else {
      $default = "someExpr(" . array_pop($this->fragments) . ")";
    }

    $fragment = "param(\"" . $node->name . "\"," . $default . "," . $type . ",";
    if (false == $node->byRef)
      $fragment .= "false";
    else
      $fragment .= "true";
    $fragment .= ")";

    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveClassConstScalar(PHPParser_Node_Scalar_ClassConst $node)
  {
    $fragment = "classConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveDirConstScalar(PHPParser_Node_Scalar_DirConst $node)
  {
    $fragment = "dirConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveDNumberScalar(PHPParser_Node_Scalar_DNumber $node)
  {
    $fragment = "float(" . sprintf('%f', $node->value) . ")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveEncapsedScalar(PHPParser_Node_Scalar_Encapsed $node)
  {
    $parts = array();
    foreach($node->parts as $item) {
      if ($item instanceof PHPParser_Node_Expr) {
	$parts[] = array_pop($this->fragments);
      } else {
	$parts[] = "scalar(string(\"" . $this->rascalizeString($item) . "\"))";
      }
    }
    $fragment = "scalar(encapsed([" . implode(",",array_reverse($parts)) . "]))";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveFileConstScalar(PHPParser_Node_Scalar_FileConst $node)
  {
    $fragment = "fileConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveFuncConstScalar(PHPParser_Node_Scalar_FuncConst $node)
  {
    $fragment = "funcConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveLineConstScalar(PHPParser_Node_Scalar_LineConst $node)
  {
    $fragment = "lineConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveLNumberScalar(PHPParser_Node_Scalar_LNumber $node)
  {
    $fragment = "integer(" . sprintf('%d',$node->value) . ")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveMethodConstScalar(PHPParser_Node_Scalar_MethodConst $node)
  {
    $fragment = "methodConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveNSConstScalar(PHPParser_Node_Scalar_NSConst $node)
  {
    $fragment = "namespaceConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveStringScalar(PHPParser_Node_Scalar_String $node)
  {
    $fragment = "string(\"" . $this->rascalizeString($node->value) . "\")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveTraitConstScalar(PHPParser_Node_Scalar_TraitConst $node)
  {
    $fragment = "traitConstant()";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveBreakStmt(PHPParser_Node_Stmt_Break $node)
  {
    if (null != $node->num)
      $fragment = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $fragment = "noExpr()";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = "\\break(" . $fragment . ")";
    return null;
  }

  public function leaveCaseStmt(PHPParser_Node_Stmt_Case $node)
  {
    if (null != $node->cond)
      $cond = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $cond = "noExpr()";

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $fragment = "\\case(" . $cond . ",[" . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveCatchStmt(PHPParser_Node_Stmt_Catch $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $xtype = array_pop($this->fragments);

    $fragment = "\\catch(" . $xtype . ",\"" . $this->rascalizeString($node->var) . "\",[" 
      . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveClassStmt(PHPParser_Node_Stmt_Class $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = array_pop($this->fragments);

    $implements = array();
    foreach($node->implements as $implemented)
      $implements[] = array_pop($this->fragments);

    if (null != $node->extends)
      $extends = array_pop($this->fragments);
    
    $fragment = "class(\"" . $this->rascalizeString($node->name) . "\"";
    $fragment .= ",";

    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $fragment .= "{" . implode(",", $modifiers) . "}";
    $fragment .= ",";

    if (null == $node->extends)
      $fragment .= "noName()";
    else
      $fragment .= "someName(".$extends.")";
    $fragment .= ",";

    $fragment .= "[" . implode(",",array_reverse($implements)) . "],[";
    $fragment .= implode(",",array_reverse($stmts))."])";
    $fragment .= $this->tagWithLine($node);

    $fragment = "classDef(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveClassConstStmt(PHPParser_Node_Stmt_ClassConst $node)
  {
    $consts = array();
    foreach($node->consts as $const)
      $consts[] = array_pop($this->fragments);

    $fragment = "const([" . implode(",", array_reverse($consts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveClassMethodStmt(PHPParser_Node_Stmt_ClassMethod $node)
  {
    $body = array();
    if (null != $node->stmts)
      foreach($node->stmts as $thestmt)
	$body[] = array_pop($this->fragments);

    $params = array();
    foreach($node->params as $param)
      $params[] = array_pop($this->fragments);

    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $byRef = "false";
    if ($node->byRef) $byRef = "true";

    $fragment = "method(\"" . $node->name . "\",{" . implode(",",$modifiers) . "}," . $byRef . ",[" 
      . implode(",",array_reverse($params)) . "],[" . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveConstStmt(PHPParser_Node_Stmt_Const $node)
  {
    $consts = array();
    foreach($node->consts as $const)
      $consts[] = array_pop($this->fragments);

    $fragment = "const([" . implode(",", array_reverse($consts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveContinueStmt(PHPParser_Node_Stmt_Continue $node)
  {
    if (null != $node->num)
      $fragment = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $fragment = "noExpr()";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = "\\continue(" . $fragment . ")";
    return null;
  }

  public function leaveDeclareStmt(PHPParser_Node_Stmt_Declare $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $decls = array();
    foreach($node->declares as $decl)
      $decls[] = array_pop($this->fragments);

    $fragment = "declare([" . implode(",", array_reverse($decls)) . "],[" . implode(",", array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveDeclareDeclareStmt(PHPParser_Node_Stmt_DeclareDeclare $node)
  {
    $fragment = "declaration(\"" . $this->rascalizeString($node->key) . "\", " . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveDoStmt(PHPParser_Node_Stmt_Do $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt) {
      $stmts[] = array_pop($this->fragments);
    }
    $fragment = "\\do(" . array_pop($this->fragments) . ",[" . implode(",",array_reverse($stmts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveEchoStmt(PHPParser_Node_Stmt_Echo $node)
  {
    $parts = array();
    foreach($node->exprs as $expr)
      $parts[] = array_pop($this->fragments);

    $fragment = "echo([" . implode(",", array_reverse($parts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveElseStmt(PHPParser_Node_Stmt_Else $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $fragment = "\\else([" . implode(",",array_reverse($body)) . "])"; 
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveElseIfStmt(PHPParser_Node_Stmt_ElseIf $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);
    
    $fragment = "elseIf(" . array_pop($this->fragments) . ",[" . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveExprStmt(PHPParser_Node_Stmt_Expr $node)
  {
    $fragment = "expr(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveForStmt(PHPParser_Node_Stmt_For $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt) {
      $stmts[] = array_pop($this->fragments);
    }
    
    $loops = array();
    foreach($node->loop as $loop) {
      $loops[] = array_pop($this->fragments);
    }

    $conds = array();
    foreach($node->cond as $cond) {
      $conds[] = array_pop($this->fragments);
    }

    $inits = array();
    foreach($node->init as $init) {
      $inits[] = array_pop($this->fragments);
    }

    $fragment = "\\for([" . implode(",", array_reverse($inits)) . "],[" . implode(",", array_reverse($conds))
      . "],[" . implode(",", array_reverse($loops)) . "],[" . implode(",", array_reverse($stmts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
    
  }
	
  public function leaveForeachStmt(PHPParser_Node_Stmt_Foreach $node)
  {
    $valueVar = array_pop($this->fragments);
    $expr = array_pop($this->fragments);
    $byRef = "false"; if ($node->byRef) $byRef = "true";

    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = array_pop($this->fragments);

    $keyvar = "noExpr()";
    if (null != $node->keyVar)
      $keyvar = "someExpr(" . array_pop($this->fragments) . ")";

    $fragment = "foreach(" . $expr . "," . $keyvar . "," . $byRef . "," . $valueVar . ",[" 
      . implode(",",array_reverse($stmts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveFunctionStmt(PHPParser_Node_Stmt_Function $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $params = array();
    foreach($node->params as $param)
      $params[] = array_pop($this->fragments);

    $byRef = "false";
    if ($node->byRef) $byRef = "true";

    $fragment = "function(\"" . $this->rascalizeString($node->name) . "\"," . $byRef 
      . ",[" . implode(",",array_reverse($params)) . "],[" . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveGlobalStmt(PHPParser_Node_Stmt_Global $node)
  {
    $vars = array();
    foreach($node->vars as $var)
      $vars[] = array_pop($this->fragments);
    
    $fragment = "global([" . implode(",",array_reverse($vars)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveGotoStmt(PHPParser_Node_Stmt_Goto $node)
  {
    $fragment = "goto(\"" . $this->rascalizeString($node->name) . "\")"; 
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveHaltCompilerStmt(PHPParser_Node_Stmt_HaltCompiler $node)
  {
    $fragment = "haltCompiler(\"" . $this->rascalizeString($node->remaining) . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveIfStmt(PHPParser_Node_Stmt_If $node)
  {
//  	echo 'Entered If on line ', $node->getLine(), ' with ', count($this->fragments), ' fragments, ';
//	if (null != $node->else)
//      echo 'an else clause, ';
//	echo count($node->elseifs), ' else-if clauses, and a body of length ', count($node->stmts);
//	echo ", current fragments are:\n";

//	print_r($this->fragments);

    $cond = array_pop($this->fragments);

    if (null != $node->else)
      $elseNode = "someElse(" . array_pop($this->fragments) . ")";
    else
      $elseNode = "noElse()";

//	echo 'Built else node: ' . $elseNode . "\n";
	
    $elseIfs = array();
    foreach($node->elseifs as $elseif)
      $elseIfs[] = array_pop($this->fragments);

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $fragment = "\\if(" . $cond . ",[" . implode(",", array_reverse($body)) . "],[" 
      . implode(",", array_reverse($elseIfs)) . "]," . $elseNode . ")";
    $fragment .= $this->tagWithLine($node);
    
//    echo 'Built fragment: ' . $fragment . "\n";
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveInlineHTMLStmt(PHPParser_Node_Stmt_InlineHTML $node)
  {
    $fragment = "inlineHTML(\"".$this->rascalizeString($node->value)."\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveInterfaceStmt(PHPParser_Node_Stmt_Interface $node)
  {
    // The fragments are in reverse order, so we first need to get out
    // the statements, and then the extends interfaces.
    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = array_pop($this->fragments);

    $extends = array();
    foreach($node->extends as $extended)
      $extends[] = array_pop($this->fragments);
    
    $fragment = "interface(\"" . $this->rascalizeString($node->name) . "\",[";
    $fragment .= implode(",",array_reverse($extends)) . "],[";
    $fragment .= implode(",",array_reverse($stmts)) . "])";
    $fragment .= $this->tagWithLine($node);

    $fragment = "interfaceDef(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveLabelStmt(PHPParser_Node_Stmt_Label $node)
  {
    $fragment = "label(\"" . $this->rascalizeString($node->name) . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leaveNamespaceStmt(PHPParser_Node_Stmt_Namespace $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    if (null != $node->name)
      $name = "someName(" . array_pop($this->fragments) . ")";
    else
      $name = "noName()";

    $fragment = "namespace(" . $name . ",[" . implode(",",array_reverse($body)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
	
  public function leavePropertyStmt(PHPParser_Node_Stmt_Property $node)
  {
    $props = array();
    foreach($node->props as $prop)
      $props[] = array_pop($this->fragments);

    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $fragment = "property({" . implode(",",$modifiers) . "},[" . implode(",",array_reverse($props)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
  
  public function leavePropertyPropertyStmt(PHPParser_Node_Stmt_PropertyProperty $node)
  {
    if (null != $node->default) {
      $fragment = "someExpr(" . array_pop($this->fragments) . ")";
    } else {
      $fragment = "noExpr()";
    }

    $fragment = "property(\"" . $node->name . "\"," . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveReturnStmt(PHPParser_Node_Stmt_Return $node)
  {
    if (null != $node->expr)
      $fragment = "someExpr(" . array_pop($this->fragments) . ")";
    else
      $fragment = "noExpr()";
    $fragment = "\\return(" . $fragment . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveStaticStmt(PHPParser_Node_Stmt_Static $node)
  {
    $staticVars = array();
    foreach($node->vars as $var)
      $staticVars[] = array_pop($this->fragments);
    $fragment = "static([" . implode(",", array_reverse($staticVars)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveStaticVarStmt(PHPParser_Node_Stmt_StaticVar $node)
  {
    $default = "noExpr()";
    if (null != $node->default)
      $default = "someExpr(" . array_pop($this->fragments) . ")";
    $fragment = "staticVar(\"" . $this->rascalizeString($node->name) . "\"," . $default . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveSwitchStmt(PHPParser_Node_Stmt_Switch $node)
  {
    $cases = array();
    foreach($node->cases as $case)
      $cases[] = array_pop($this->fragments);

    $fragment = "\\switch(" . array_pop($this->fragments) . ",[" . implode(",",array_reverse($cases)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveThrowStmt(PHPParser_Node_Stmt_Throw $node)
  {
    $fragment = "\\throw(" . array_pop($this->fragments) . ")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

/* 	public function leaveTraitStmt(PHPParser_Node_Stmt_Trait $node) */
/* 	{ */
/* 		return null; */
/* 	} */
/* 	public function leaveTraitUseStmt(PHPParser_Node_Stmt_TraitUse $node) */
/* 	{ */
/* 		return null; */
/* 	} */
/* 	public function leaveAliasTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Alias $node) */
/* 	{ */
/* 		return null; */
/* 	} */
/* 	public function leavePrecedenceTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Precedence $node) */
/* 	{ */
/* 		return null; */
/* 	} */

  public function leaveTryCatchStmt(PHPParser_Node_Stmt_TryCatch $node)
  {
    $catches = array();
    foreach($node->catches as $toCatch)
      $catches[] = array_pop($this->fragments);

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = array_pop($this->fragments);

    $fragment = "tryCatch([" . implode(",", array_reverse($body)) . "],[" . implode(",",array_reverse($catches)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveUnsetStmt(PHPParser_Node_Stmt_Unset $node)
  {
    $vars = array();
    foreach($node->vars as $var)
      $vars[] = array_pop($this->fragments);
    $fragment = "unset([" . implode(",", array_reverse($vars)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveUseStmt(PHPParser_Node_Stmt_Use $node)
  {
    $uses = array();
    foreach($node->uses as $use)
      $uses[] = array_pop($this->fragments);
    $fragment = "use([" . implode(",", array_reverse($uses)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function leaveUseUseStmt(PHPParser_Node_Stmt_UseUse $node)
  {
    $aliasName = array_pop($this->fragments);
    $fragment = "use(" . $aliasName . "," . "\"" . $node->alias . "\")";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }
  
  public function leaveWhileStmt(PHPParser_Node_Stmt_While $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt) {
      $stmts[] = array_pop($this->fragments);
    }
    $fragment = "\\while(" . array_pop($this->fragments) . ",[" . implode(",",array_reverse($stmts)) . "])";
    $fragment .= $this->tagWithLine($node);
    $this->fragments[] = $fragment;
    return null;
  }

  public function getRascalizedAST()
  {
    return "script([".implode(",\n",$this->fragments)."])";
  }
}

if (count($argv) != 2) {
  echo "Expected exactly 1 argument\n";
  exit -1;
}

$file = $argv[1];

$inputCode = '';
if (file_exists($file))
  $inputCode = file_get_contents($file);

$parser = new PHPParser_Parser;
$traverser = new PHPParser_NodeTraverser;
$visitor = new AST2Rascal($file);
$useful = new UsefulVisitor($visitor);
$traverser->addVisitor($useful);
$dumper = new PHPParser_NodeDumper;

try {
  $stmts = $parser->parse(new PHPParser_Lexer($inputCode));
/*   echo htmlspecialchars($dumper->dump($stmts)); */
  $stmts = $traverser->traverse($stmts);
} catch (PHPParser_Error $e) {
  echo 'Parse Error: ', $e->getMessage();
}

echo $visitor->getRascalizedAST();
?>