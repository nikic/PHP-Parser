<?php
require '../bootstrap.php';
require_once 'IPrinter.php';
require_once 'BasePrinter.php';

ini_set('xdebug.max_nesting_level', 2000);

class AST2Rascal extends BasePrinter {
  private $filename = "";
  private $addLocations = FALSE;
  private $relativeLocations = FALSE;
  private $addIds = FALSE;
  private $idPrefix = "";

  private $insideTrait = FALSE;
  
  private $currentFunction = "";
  private $currentClass = "";
  private $currentTrait = "";
  private $currentMethod = "";
  private $currentNamespace = "";
  
  
  public function AST2Rascal($str, $locs, $rel, $ids, $prefix)
  {
    $this->filename = $str;
    $this->addLocations = $locs;
    $this->relativeLocations = $rel;
    $this->addIds = $ids;
    $this->idPrefix = $prefix;    
  }

  public function rascalizeString($str) 
  {
    return addcslashes($str, "<>'\n\t\r\\\"");
  }

  private function addUniqueId() {
    $idToAdd = uniqid($this->idPrefix, true);
  	return "@id=\"{$this->rascalizeString($idToAdd)}\"";
  }
  
  private function addLocationTag(PHPParser_Node $node) {
  	if ($this->relativeLocations) {
  		return "@at=|home://{$this->filename}|({$node->getOffset()},{$node->getLength()},<{$node->getLine()},0>,<{$node->getLine()},0>)";
  	} else {
  		return "@at=|file://{$this->filename}|({$node->getOffset()},{$node->getLength()},<{$node->getLine()},0>,<{$node->getLine()},0>)";
  	}
  }
  
  private function annotateASTNode(PHPParser_Node $node)
  {
    $tagsToAdd = array();
  	if ($this->addLocations)
  		$tagsToAdd[] = $this->addLocationTag($node);
  	if ($this->addIds)
  		$tagsToAdd[] = $this->addUniqueId();
    
    if (count($tagsToAdd) > 0)
    	return "[" . implode(",",$tagsToAdd) . "]";
    return "";
  }

  public function pprintArg(PHPParser_Node_Arg $node)
  {
    $argValue = $this->pprint($node->value);

    if ($node->byRef)
      $byRef = "true";
    else
      $byRef = "false";
    
    $fragment = "actualParameter(" . $argValue . "," . $byRef . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintConst(PHPParser_Node_Const $node)
  {
    $fragment = "const(\"" . $node->name . "\"," . $this->pprint($node->value) . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintArrayExpr(PHPParser_Node_Expr_Array $node)
  {
    $items = array();
    foreach($node->items as $item)
      $items[] = $this->pprint($item);

    $fragment = "array([" . implode(",",$items) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintArrayDimFetchExpr(PHPParser_Node_Expr_ArrayDimFetch $node)
  {
    if (null != $node->dim)
      $dim = "someExpr(" . $this->pprint($node->dim) . ")";
    else
      $dim = "noExpr()";

    $fragment = "fetchArrayDim(" . $this->pprint($node->var) . "," . $dim . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintArrayItemExpr(PHPParser_Node_Expr_ArrayItem $node)
  {
    $nodeValue = $this->pprint($node->value);
    
    if (null == $node->key)
      $key = "noExpr()";
    else
      $key = "someExpr(" . $this->pprint($node->key) . ")";
    
    if ($node->byRef)
      $byRef = "true";
    else
      $byRef = "false";

    $fragment = "arrayElement(" . $key . "," . $nodeValue . "," . $byRef . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintAssignExpr(PHPParser_Node_Expr_Assign $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assign(".$assignVar.",".$assignExpr.")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintAssignBitwiseAndExpr(PHPParser_Node_Expr_AssignBitwiseAnd $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseAnd())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignBitwiseOrExpr(PHPParser_Node_Expr_AssignBitwiseOr $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseOr())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignBitwiseXorExpr(PHPParser_Node_Expr_AssignBitwiseXor $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",bitwiseXor())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignConcatExpr(PHPParser_Node_Expr_AssignConcat $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",concat())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignDivExpr(PHPParser_Node_Expr_AssignDiv $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",div())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintAssignMinusExpr(PHPParser_Node_Expr_AssignMinus $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",minus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignModExpr(PHPParser_Node_Expr_AssignMod $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",\\mod())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignMulExpr(PHPParser_Node_Expr_AssignMul $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",mul())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignPlusExpr(PHPParser_Node_Expr_AssignPlus $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",plus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignRefExpr(PHPParser_Node_Expr_AssignRef $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "refAssign(".$assignVar.",".$assignExpr.")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignShiftLeftExpr(PHPParser_Node_Expr_AssignShiftLeft $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",leftShift())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAssignShiftRightExpr(PHPParser_Node_Expr_AssignShiftRight $node)
  {
    $assignExpr = $this->pprint($node->expr);
    $assignVar = $this->pprint($node->var);

    $fragment = "assignWOp(".$assignVar.",".$assignExpr.",rightShift())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintBitwiseAndExpr(PHPParser_Node_Expr_BitwiseAnd $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseAnd())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintBitwiseNotExpr(PHPParser_Node_Expr_BitwiseNot $node)
  {
    $expr = $this->pprint($node->expr);

    $fragment = "unaryOperation(".$expr.",bitwiseNot())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
  
  public function pprintBitwiseOrExpr(PHPParser_Node_Expr_BitwiseOr $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseOr())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintBitwiseXorExpr(PHPParser_Node_Expr_BitwiseXor $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",bitwiseXor())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintBooleanAndExpr(PHPParser_Node_Expr_BooleanAnd $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",booleanAnd())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintBooleanNotExpr(PHPParser_Node_Expr_BooleanNot $node)
  {
    $expr = $this->pprint($node->expr);

    $fragment = "unaryOperation(".$expr.",booleanNot())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintBooleanOrExpr(PHPParser_Node_Expr_BooleanOr $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",booleanOr())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintArrayCastExpr(PHPParser_Node_Expr_Cast_Array $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(array()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintBoolCastExpr(PHPParser_Node_Expr_Cast_Bool $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(\\bool()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintDoubleCastExpr(PHPParser_Node_Expr_Cast_Double $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(float()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintIntCastExpr(PHPParser_Node_Expr_Cast_Int $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(\\int()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintObjectCastExpr(PHPParser_Node_Expr_Cast_Object $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(object()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintStringCastExpr(PHPParser_Node_Expr_Cast_String $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(string()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintUnsetCastExpr(PHPParser_Node_Expr_Cast_Unset $node)
  {
    $toCast = $this->pprint($node->expr);
    $fragment = "cast(unset()," . $toCast . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintClassConstFetchExpr(PHPParser_Node_Expr_ClassConstFetch $node)
  {
    $name = $this->pprint($node->class);
    if ($node->class instanceof PHPParser_Node_Name)
      $name = "name({$name})";
    else
      $name = "expr({$name})";

    $fragment = "fetchClassConst(" . $name . ",\"" . $node->name . "\")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintCloneExpr(PHPParser_Node_Expr_Clone $node)
  {
    $fragment = "clone(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintClosureExpr(PHPParser_Node_Expr_Closure $node)
  {
    $body = array();
    $params = array();
    $uses = array();

    foreach($node->uses as $use)
      $uses[] = $this->pprint($use);
    foreach($node->params as $param)
      $params[] = $this->pprint($param);
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);
    
    $fragment = "closure([" . implode(",",$body) . "],[";
    $fragment .= implode(",",$params) . "],[";
    $fragment .= implode(",",$uses) . "],";
    if ($node->byRef)
      $fragment .= "true,";
    else
      $fragment .= "false,";
    if ($node->static)
      $fragment .= "true";
    else
      $fragment .= "false";
    $fragment .= ")";
    
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintClosureUseExpr(PHPParser_Node_Expr_ClosureUse $node)
  {
    $fragment = "closureUse(\"" . $node->var . "\",";
    if ($node->byRef)
      $fragment .= "true";
    else
      $fragment .= "false";
    $fragment .= ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintConcatExpr(PHPParser_Node_Expr_Concat $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",concat())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintConstFetchExpr(PHPParser_Node_Expr_ConstFetch $node)
  {
    $fragment = "fetchConst(" . $this->pprint($node->name) . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintDivExpr(PHPParser_Node_Expr_Div $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",div())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintEmptyExpr(PHPParser_Node_Expr_Empty $node)
  {
    $fragment = "empty(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
  
  public function pprintEqualExpr(PHPParser_Node_Expr_Equal $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",equal())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintErrorSuppressExpr(PHPParser_Node_Expr_ErrorSuppress $node)
  {
    $fragment = "suppress(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintEvalExpr(PHPParser_Node_Expr_Eval $node)
  {
    $fragment = "eval(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintExitExpr(PHPParser_Node_Expr_Exit $node)
  {
    if (null != $node->expr)
      $fragment = "someExpr(" . $this->pprint($node->expr) . ")";
    else
      $fragment = "noExpr()";
    $fragment = "exit(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintFuncCallExpr(PHPParser_Node_Expr_FuncCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = $this->pprint($arg);

    $name = $this->pprint($node->name);
    if ($node->name instanceof PHPParser_Node_Name)
      $name = "name({$name})";
    else
      $name = "expr({$name})";

    $fragment = "call(" . $name . ",[" . implode(",",$args) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintGreaterExpr(PHPParser_Node_Expr_Greater $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",gt())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintGreaterOrEqualExpr(PHPParser_Node_Expr_GreaterOrEqual $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",geq())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintIdenticalExpr(PHPParser_Node_Expr_Identical $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",identical())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintIncludeExpr(PHPParser_Node_Expr_Include $node)
  {
    $fragment = "include(" . $this->pprint($node->expr) . ",";
    if (PHPParser_Node_Expr_Include::TYPE_INCLUDE == $node->type)
      $fragment .= "include()";
    elseif (PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE == $node->type)
      $fragment .= "includeOnce()";
    elseif (PHPParser_Node_Expr_Include::TYPE_REQUIRE == $node->type)
      $fragment .= "require()";
    else
      $fragment .= "requireOnce()";
    $fragment .= ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintInstanceofExpr(PHPParser_Node_Expr_Instanceof $node)
  {
    $right = $this->pprint($node->class);
    if ($node->class instanceOf PHPParser_Node_Name)
      $right = "name({$right})";
    else
      $right = "expr({$right})";

    $left = $this->pprint($node->expr);

    $fragment = "instanceOf(".$left.",".$right.")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintIssetExpr(PHPParser_Node_Expr_Isset $node)
  {
    $exprs = array();
    foreach($node->vars as $var)
      $exprs[] = $this->pprint($var);

    $fragment = "isSet([" . implode(",",$exprs) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintListExpr(PHPParser_Node_Expr_List $node)
  {
    $exprs = array();
    foreach($node->vars as $var)
      if (null != $var)
        $exprs[] = "someExpr(" . $this->pprint($var) . ")";
      else
        $exprs[] = "noExpr()";
      
    $fragment = "listExpr([" . implode(",",$exprs) . "])";
    $fragment .= $this->annotateASTNode($node);
    
    return $fragment;
  }
  
  public function pprintLogicalAndExpr(PHPParser_Node_Expr_LogicalAnd $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",logicalAnd())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintLogicalOrExpr(PHPParser_Node_Expr_LogicalOr $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",logicalOr())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
  	
  public function pprintLogicalXorExpr(PHPParser_Node_Expr_LogicalXor $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",logicalXor())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintMethodCallExpr(PHPParser_Node_Expr_MethodCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = $this->pprint($arg);

    if ($node->name instanceof PHPParser_Node_Expr) {
      $name = $this->pprint($node->name);
      $name = "expr({$name})";
    } else {
      $name = "name(name(\"" . $node->name . "\"))";
    }

    $target = $this->pprint($node->var);

    $fragment = "methodCall(" . $target . "," . $name . ",[" . implode(",",$args) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintMinusExpr(PHPParser_Node_Expr_Minus $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",minus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintModExpr(PHPParser_Node_Expr_Mod $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",\\mod())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintMulExpr(PHPParser_Node_Expr_Mul $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",mul())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintNewExpr(PHPParser_Node_Expr_New $node)
  {
    $args = array();
    foreach ($node->args as $arg)
      $args[] = $this->pprint($arg);

    $name = $this->pprint($node->class);

    if ($node->class instanceof PHPParser_Node_Expr)
      $name = "expr({$name})";
    else
      $name = "name({$name})";

    $fragment = "new(" . $name . ",[" . implode(",",$args) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintNotEqualExpr(PHPParser_Node_Expr_NotEqual $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",notEqual())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintNotIdenticalExpr(PHPParser_Node_Expr_NotIdentical $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",notIdentical())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintPlusExpr(PHPParser_Node_Expr_Plus $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",plus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintPostDecExpr(PHPParser_Node_Expr_PostDec $node)
  {
    $operand = $this->pprint($node->var);
    $fragment = "unaryOperation(".$operand.",postDec())";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintPostIncExpr(PHPParser_Node_Expr_PostInc $node)
  {
    $operand = $this->pprint($node->var);
    $fragment = "unaryOperation(".$operand.",postInc())";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintPreDecExpr(PHPParser_Node_Expr_PreDec $node)
  {
    $operand = $this->pprint($node->var);
    $fragment = "unaryOperation(".$operand.",preDec())";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintPreIncExpr(PHPParser_Node_Expr_PreInc $node)
  {
    $operand = $this->pprint($node->var);
    $fragment = "unaryOperation(".$operand.",preInc())";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }

  public function pprintPrintExpr(PHPParser_Node_Expr_Print $node)
  {
    $operand = $this->pprint($node->expr);
    $fragment = "print(" . $operand . ")";
    $fragment .= $this->annotateASTNode($node);
    return $fragment;
  }
	
  public function pprintPropertyFetchExpr(PHPParser_Node_Expr_PropertyFetch $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $fragment = "expr(" . $this->pprint($node->name) . ")";
    } else {
      $fragment = "name(name(\"" . $node->name . "\"))";
    }

    $fragment = "propertyFetch(" . $this->pprint($node->var) . "," . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintShellExecExpr(PHPParser_Node_Expr_ShellExec $node)
  {
    $parts = array();
    foreach($node->parts as $item) {
      if ($item instanceof PHPParser_Node_Expr) {
	$parts[] = $this->pprint($item);
      } else {
	$parts[] = "scalar(string(\"" . $this->rascalizeString($item) . "\"))";
      }
    }

    $fragment = "shellExec([" . implode(",",$parts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintShiftLeftExpr(PHPParser_Node_Expr_ShiftLeft $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",leftShift())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintShiftRightExpr(PHPParser_Node_Expr_ShiftRight $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",rightShift())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintSmallerExpr(PHPParser_Node_Expr_Smaller $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",lt())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintSmallerOrEqualExpr(PHPParser_Node_Expr_SmallerOrEqual $node)
  {
    $right = $this->pprint($node->right);
    $left = $this->pprint($node->left);

    $fragment = "binaryOperation(".$left.",".$right.",leq())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintStaticCallExpr(PHPParser_Node_Expr_StaticCall $node)
  {
    $args = array();
    foreach($node->args as $arg)
      $args[] = $this->pprint($arg);

    if ($node->name instanceof PHPParser_Node_Expr)
      $name = "expr(" . $this->pprint($node->name) . ")";
    else
      $name = "name(name(\"" . $node->name . "\"))";

    if ($node->class instanceof PHPParser_Node_Expr) {
      $class = "expr(" . $this->pprint($node->class) . ")";
    } else {
      $class = "name(" . $this->pprint($node->class) . ")";
    }

    $fragment = "staticCall({$class},{$name},[" . implode(",",$args) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintStaticPropertyFetchExpr(PHPParser_Node_Expr_StaticPropertyFetch $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $name = "expr(" . $this->pprint($node->name) . ")";
    } else {
      $name = "name(name(\"" . $node->name . "\"))";
    }

    if ($node->class instanceof PHPParser_Node_Expr) {
      $class = "expr(" . $this->pprint($node->class) . ")";
    } else {
      $class = "name(" . $this->pprint($node->class) . ")";
    }

    $fragment = "staticPropertyFetch({$class},{$name})";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintTernaryExpr(PHPParser_Node_Expr_Ternary $node)
  {
    $else = $this->pprint($node->else);
    if (null != $node->if)
      $if = "someExpr(" . $this->pprint($node->if) . ")";
    else
      $if = "noExpr()";
    $cond = $this->pprint($node->cond);
    
    $fragment = "ternary({$cond},{$if},{$else})";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintUnaryMinusExpr(PHPParser_Node_Expr_UnaryMinus $node)
  {
    $operand = $this->pprint($node->expr);
    $fragment = "unaryOperation(".$operand.",unaryMinus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintUnaryPlusExpr(PHPParser_Node_Expr_UnaryPlus $node)
  {
    $operand = $this->pprint($node->expr);
    $fragment = "unaryOperation(".$operand.",unaryPlus())";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintVariableExpr(PHPParser_Node_Expr_Variable $node)
  {
    if ($node->name instanceof PHPParser_Node_Expr) {
      $fragment = "expr(" . $this->pprint($node->name) . ")";
    } else {
      $fragment = "name(name(\"" . $node->name . "\"))";
    }
    $fragment = "var(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintYieldExpr(PHPParser_Node_Expr_Yield $node)
  {
    if (null != $node->value)
      $valuePart = "someExpr(" . $this->pprint($node->value) . ")";
    else
      $valuePart = "noExpr()";
      
    if (null != $node->key)
      $keyPart = "someExpr(" . $this->pprint($node->key) . ")";
    else
      $keyPart = "noExpr()";
      
    $fragment = "yield({$keyPart},{$valuePart})";
    $fragment .= $this->annotateASTNode($node);
    
    return $fragment;
  }
  
  public function pprintFullyQualifiedName(PHPParser_Node_Name_FullyQualified $node)
  {
    return $this->pprintName($node);
  }
	
  public function pprintRelativeName(PHPParser_Node_Name_Relative $node)
  {
    return $this->pprintName($node);
  }

  public function pprintName(PHPParser_Node_Name $node)
  {
    if (is_array($node->parts))
      $fragment = implode("::",$node->parts);
    else
      $fragment = $node->parts;
    $fragment = "name(\"" . $fragment . "\")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintParam(PHPParser_Node_Param $node)
  {
    if (null == $node->type) {
      $type = "noName()";
    } else {
      if ($node->type instanceof PHPParser_Node_Name) {
	$type = "someName(" . $this->pprint($node->type) . ")";
      } else {
	$type = "someName(name(\"" . $node->type . "\"))";
      }
    }

    if (null == $node->default) {
      $default = "noExpr()";
    } else {
      $default = "someExpr(" . $this->pprint($node->default) . ")";
    }

    $fragment = "param(\"" . $node->name . "\"," . $default . "," . $type . ",";
    if (false == $node->byRef)
      $fragment .= "false";
    else
      $fragment .= "true";
    $fragment .= ")";

    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintClassConstScalar(PHPParser_Node_Scalar_ClassConst $node)
  {
    // If we are inside a trait and find __CLASS__, we have no clue what it should
    // be, so leave it unresolved for now; else tag it with the class we are actually
    // inside at the moment.
  	if ($this->insideTrait) {
	    $fragment = "classConstant()";
	} else {
	    $fragment = "classConstant()[@actualValue=\"{$this->currentClass}\"]";
	}	
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintDirConstScalar(PHPParser_Node_Scalar_DirConst $node)
  {
    $fragment = "dirConstant()[@actualValue=\"{dirname($this->filename)}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintDNumberScalar(PHPParser_Node_Scalar_DNumber $node)
  {
    $fragment = "float(" . sprintf('%f', $node->value) . ")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintEncapsedScalar(PHPParser_Node_Scalar_Encapsed $node)
  {
    $parts = array();
    foreach($node->parts as $item) {
      if ($item instanceof PHPParser_Node_Expr) {
	$parts[] = $this->pprint($item);
      } else {
	$parts[] = "scalar(string(\"" . $this->rascalizeString($item) . "\"))";
      }
    }
    $fragment = "scalar(encapsed([" . implode(",",$parts) . "]))";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintFileConstScalar(PHPParser_Node_Scalar_FileConst $node)
  {
    $fragment = "fileConstant()[@actualValue=\"{$this->filename}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintFuncConstScalar(PHPParser_Node_Scalar_FuncConst $node)
  {
    $fragment = "funcConstant()[@actualValue=\"{$this->currentFunction}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintLineConstScalar(PHPParser_Node_Scalar_LineConst $node)
  {
    $fragment = "lineConstant()[@actualValue=\"{$node->getLine()}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintLNumberScalar(PHPParser_Node_Scalar_LNumber $node)
  {
    $fragment = "integer(" . sprintf('%d',$node->value) . ")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintMethodConstScalar(PHPParser_Node_Scalar_MethodConst $node)
  {
    $fragment = "methodConstant()[@actualValue=\"{$this->currentMethod}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintNSConstScalar(PHPParser_Node_Scalar_NSConst $node)
  {
    $fragment = "namespaceConstant()[@actualValue=\"{$this->currentNamespace}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintStringScalar(PHPParser_Node_Scalar_String $node)
  {
    $fragment = "string(\"" . $this->rascalizeString($node->value) . "\")";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintTraitConstScalar(PHPParser_Node_Scalar_TraitConst $node)
  {
    $fragment = "traitConstant()[@actualValue=\"{$this->currentTrait}\"]";
    $fragment = "scalar(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintBreakStmt(PHPParser_Node_Stmt_Break $node)
  {
    if (null != $node->num)
      $fragment = "someExpr(" . $this->pprint($node->num) . ")";
    else
      $fragment = "noExpr()";

    $fragment = "\\break(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintCaseStmt(PHPParser_Node_Stmt_Case $node)
  {
    if (null != $node->cond)
      $cond = "someExpr(" . $this->pprint($node->cond) . ")";
    else
      $cond = "noExpr()";

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $fragment = "\\case(" . $cond . ",[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintCatchStmt(PHPParser_Node_Stmt_Catch $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $xtype = $this->pprint($node->type);

    $fragment = "\\catch(" . $xtype . ",\"" . $node->var . "\",[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintClassStmt(PHPParser_Node_Stmt_Class $node)
  {
    $priorClass = $this->currentClass;
    if (strlen($this->currentNamespace) > 0)
	    $this->currentClass = $this->currentNamespace . "\\" . $node->name;
	else
		$this->currentClass = $node->name;

    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = $this->pprint($stmt);

    $implements = array();
    foreach($node->implements as $implemented)
      $implements[] = $this->pprint($implemented);

    if (null != $node->extends)
      $extends = "someName(" . $this->pprint($node->extends) . ")";
    else
      $extends = "noName()";
    
    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $fragment = "class(\"" . $node->name . "\",{" . implode(",", $modifiers) . "}," . $extends . ",";
    $fragment .= "[" . implode(",",$implements) . "],[";
    $fragment .= implode(",",$stmts)."])";
    $fragment .= $this->annotateASTNode($node);

    $fragment = "classDef(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

	$this->currentClass = $priorClass;
	
    return $fragment;
  }

  public function pprintClassConstStmt(PHPParser_Node_Stmt_ClassConst $node)
  {
    $consts = array();
    foreach($node->consts as $const)
      $consts[] = $this->pprint($const);

    $fragment = "constCI([" . implode(",", $consts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintClassMethodStmt(PHPParser_Node_Stmt_ClassMethod $node)
  {
  	$priorMethod = $this->currentMethod;
  	$this->currentMethod = $node->name;
  	
    $body = array();
    if (null != $node->stmts)
      foreach($node->stmts as $thestmt)
	$body[] = $this->pprint($thestmt);

    $params = array();
    foreach($node->params as $param)
      $params[] = $this->pprint($param);

    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $byRef = "false";
    if ($node->byRef) $byRef = "true";

    $fragment = "method(\"" . $node->name . "\",{" . implode(",",$modifiers) . "}," . $byRef . ",[" 
      . implode(",",$params) . "],[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

	$this->currentMethod = $priorMethod;
	
    return $fragment;
  }
	
  public function pprintConstStmt(PHPParser_Node_Stmt_Const $node)
  {
    $consts = array();
    foreach($node->consts as $const)
      $consts[] = $this->pprint($const);

    $fragment = "const([" . implode(",", $consts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintContinueStmt(PHPParser_Node_Stmt_Continue $node)
  {
    if (null != $node->num)
      $fragment = "someExpr(" . $this->pprint($node->num) . ")";
    else
      $fragment = "noExpr()";

    $fragment = "\\continue(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintDeclareStmt(PHPParser_Node_Stmt_Declare $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $decls = array();
    foreach($node->declares as $decl)
      $decls[] = $this->pprint($decl);

    $fragment = "declare([" . implode(",", $decls) . "],[" . implode(",", $body) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintDeclareDeclareStmt(PHPParser_Node_Stmt_DeclareDeclare $node)
  {
    $fragment = "declaration(\"" . $node->key . "\", " . $this->pprint($node->value) . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintDoStmt(PHPParser_Node_Stmt_Do $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = $this->pprint($stmt);

    $fragment = "\\do(" . $this->pprint($node->cond) . ",[" . implode(",",$stmts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintEchoStmt(PHPParser_Node_Stmt_Echo $node)
  {
    $parts = array();
    foreach($node->exprs as $expr)
      $parts[] = $this->pprint($expr);

    $fragment = "echo([" . implode(",", $parts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintElseStmt(PHPParser_Node_Stmt_Else $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $fragment = "\\else([" . implode(",",$body) . "])"; 
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintElseIfStmt(PHPParser_Node_Stmt_ElseIf $node)
  {
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);
    
    $fragment = "elseIf(" . $this->pprint($node->cond) . ",[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintExprStmt(PHPParser_Node_Stmt_Expr $node)
  {
    $fragment = "exprstmt(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintForStmt(PHPParser_Node_Stmt_For $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = $this->pprint($stmt);
    
    $loops = array();
    foreach($node->loop as $loop)
      $loops[] = $this->pprint($loop);

    $conds = array();
    foreach($node->cond as $cond)
      $conds[] = $this->pprint($cond);

    $inits = array();
    foreach($node->init as $init)
      $inits[] = $this->pprint($init);

    $fragment = "\\for([" . implode(",", $inits) . "],[" . implode(",", $conds) . "],[" . implode(",", $loops) . "],[" . implode(",", $stmts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;    
  }
	
  public function pprintForeachStmt(PHPParser_Node_Stmt_Foreach $node)
  {
    $valueVar = $this->pprint($node->valueVar);
    $expr = $this->pprint($node->expr);
    $byRef = "false"; if ($node->byRef) $byRef = "true";

    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = $this->pprint($stmt);

    $keyvar = "noExpr()";
    if (null != $node->keyVar)
      $keyvar = "someExpr(" . $this->pprint($node->keyVar) . ")";

    $fragment = "foreach(" . $expr . "," . $keyvar . "," . $byRef . "," . $valueVar . ",[" 
      . implode(",",$stmts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintFunctionStmt(PHPParser_Node_Stmt_Function $node)
  {
  	$priorFunction = $this->currentFunction;
  	$this->currentFunction = $node->name;
  	
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $params = array();
    foreach($node->params as $param)
      $params[] = $this->pprint($param);

    $byRef = "false";
    if ($node->byRef) $byRef = "true";

    $fragment = "function(\"" . $node->name . "\"," . $byRef 
      . ",[" . implode(",",$params) . "],[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

	$this->currentFunction = $priorFunction;
	
    return $fragment;
  }

  public function pprintGlobalStmt(PHPParser_Node_Stmt_Global $node)
  {
    $vars = array();
    foreach($node->vars as $var)
      $vars[] = $this->pprint($var);
    
    $fragment = "global([" . implode(",",$vars) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintGotoStmt(PHPParser_Node_Stmt_Goto $node)
  {
    $fragment = "goto(\"" . $node->name . "\")"; 
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintHaltCompilerStmt(PHPParser_Node_Stmt_HaltCompiler $node)
  {
    $fragment = "haltCompiler(\"" . $this->rascalizeString($node->remaining) . "\")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintIfStmt(PHPParser_Node_Stmt_If $node)
  {
    $cond = $this->pprint($node->cond);

    if (null != $node->else)
      $elseNode = "someElse(" . $this->pprint($node->else) . ")";
    else
      $elseNode = "noElse()";

    $elseIfs = array();
    foreach($node->elseifs as $elseif)
      $elseIfs[] = $this->pprint($elseif);

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $fragment = "\\if(" . $cond . ",[" . implode(",", $body) . "],[" 
      . implode(",", $elseIfs) . "]," . $elseNode . ")";
    $fragment .= $this->annotateASTNode($node);
    
    return $fragment;
  }

  public function pprintInlineHTMLStmt(PHPParser_Node_Stmt_InlineHTML $node)
  {
    $fragment = "inlineHTML(\"".$this->rascalizeString($node->value)."\")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintInterfaceStmt(PHPParser_Node_Stmt_Interface $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt)
      $stmts[] = $this->pprint($stmt);

    $extends = array();
    foreach($node->extends as $extended)
      $extends[] = $this->pprint($extended);
    
    $fragment = "interface(\"" . $node->name . "\",[";
    $fragment .= implode(",",$extends) . "],[";
    $fragment .= implode(",",$stmts) . "])";
    $fragment .= $this->annotateASTNode($node);

    $fragment = "interfaceDef(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintLabelStmt(PHPParser_Node_Stmt_Label $node)
  {
    $fragment = "label(\"" . $node->name . "\")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintNamespaceStmt(PHPParser_Node_Stmt_Namespace $node)
  {
  	// If we have a non-null name, set this to the namespace name; if we
  	// don't, this is a global namespace declaration, like
  	// namespace { global stuff }
  	$priorNamespace = $this->currentNamespace;
  	if (null != $node->name)
  		$this->currentNamespace = $node->name;
  	else
  		$this->currentNamespace = "";
  		
    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

	// Again check the name -- since it is optional, we return an OptionName
	// here, which could be noName() if this is a global namespace
    if (null != $node->name) {
      $headerName = $this->pprint($node->name);
      $name = "someName({$headerName})";
    } else {
      $name = "noName()";
    }

	// The third option shouldn't occur, but is put in just in case; the first
	// option is the case where we have a body, the second is where we have
	// a namespace header, like namespace DB; that opens a new block but doesn't
	// enclose it in braces
    if (null != $node->stmts)
      $fragment = "namespace(" . $name . ",[" . implode(",",$body) . "])";
    else if (null != $node->name)
      $fragment = "namespaceHeader({$this->pprint($node->name)})";
    else
      $fragment = "namespaceHeader({$this->pprint("")})";
       
    $fragment .= $this->annotateASTNode($node);

	// If we had a statement body, then we reset the namespace at the end; if
	// we didn't, it means that we just had a namespace declaration like
	// namespace DB; which had no body, but then is still active at the end
	// in which case we don't want to reset it
	if (null != $node->stmts)
		$this->currentNamespace = $priorNamespace;
		
    return $fragment;
  }
	
  public function pprintPropertyStmt(PHPParser_Node_Stmt_Property $node)
  {
    $props = array();
    foreach($node->props as $prop)
      $props[] = $this->pprint($prop);

    $modifiers = array();
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\\public()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\\private()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
    if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";

    $fragment = "property({" . implode(",",$modifiers) . "},[" . implode(",",$props) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
  
  public function pprintPropertyPropertyStmt(PHPParser_Node_Stmt_PropertyProperty $node)
  {
    if (null != $node->default) {
      $fragment = "someExpr(" . $this->pprint($node->default) . ")";
    } else {
      $fragment = "noExpr()";
    }

    $fragment = "property(\"" . $node->name . "\"," . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintReturnStmt(PHPParser_Node_Stmt_Return $node)
  {
    if (null != $node->expr)
      $fragment = "someExpr(" . $this->pprint($node->expr) . ")";
    else
      $fragment = "noExpr()";
    $fragment = "\\return(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintStaticStmt(PHPParser_Node_Stmt_Static $node)
  {
    $staticVars = array();
    foreach($node->vars as $var)
      $staticVars[] = $this->pprint($var);

    $fragment = "static([" . implode(",", $staticVars) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintStaticVarStmt(PHPParser_Node_Stmt_StaticVar $node)
  {
    $default = "noExpr()";
    if (null != $node->default)
      $default = "someExpr(" . $this->pprint($node->default) . ")";

    $fragment = "staticVar(\"" . $node->name . "\"," . $default . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintSwitchStmt(PHPParser_Node_Stmt_Switch $node)
  {
    $cases = array();
    foreach($node->cases as $case)
      $cases[] = $this->pprint($case);

    $fragment = "\\switch(" . $this->pprint($node->cond) . ",[" . implode(",",$cases) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintThrowStmt(PHPParser_Node_Stmt_Throw $node)
  {
    $fragment = "\\throw(" . $this->pprint($node->expr) . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintTraitStmt(PHPParser_Node_Stmt_Trait $node)
  {
    $body = array();

    $priorTrait = $this->currentTrait;
    $this->insideTrait = TRUE;
    
    if (strlen($this->currentNamespace) > 0)
	    $this->currentTrait = $this->currentNamespace . "\\" . $node->name;
	else
		$this->currentTrait = $node->name;
		
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    $fragment = "trait(\"" . $node->name . "\",[" . implode(",",$body) . "])";
    $fragment .= $this->annotateASTNode($node);

    $fragment = "traitDef(" . $fragment . ")";
    $fragment .= $this->annotateASTNode($node);

	$this->currentTrait = $priorTrait;
	$this->insideTrait = FALSE;
	
    return $fragment;
  }

  public function pprintTraitUseStmt(PHPParser_Node_Stmt_TraitUse $node)
  {
    $adaptations = array();
    foreach($node->adaptations as $adaptation)
      $adaptations[] = $this->pprint($adaptation);

    $traits = array();
    foreach($node->traits as $trait)
      $traits[] = $this->pprint($trait);

    $fragment = "traitUse([" . implode(",",$traits) . "],[" . implode(",",$adaptations) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintAliasTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Alias $node)
  {
    if (null != $node->newName) {
      $newName = "someName(name(\"" . $node->newName . "\"))";
    } else {
      $newName = "noName()";
    }

    if (null != $node->newModifier) {
      $modifiers = array();
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) $modifiers[] = "\\public()";
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) $modifiers[] = "protected()";
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) $modifiers[] = "\\private()";
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) $modifiers[] = "abstract()";
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) $modifiers[] = "final()";
      if ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) $modifiers[] = "static()";
      $newModifier = "{ " . implode(",",$modifiers) . " }";
    } else {
      $newModifier = "{ }";
    }

    $newMethod = "\"" . $node->method . "\"";

    if (null != $node->trait) {
      $trait = "someName(" . $this->pprint($node->trait) . ")";
    } else {
      $trait = "noName()";
    }

    $fragment = "traitAlias(" . $trait . "," . $newMethod . "," . $newModifier . "," . $newName . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
	
  public function pprintPrecedenceTraitUseAdaptationStmt(PHPParser_Node_Stmt_TraitUseAdaptation_Precedence $node)
  {
    $insteadOf = array();
    foreach($node->insteadof as $item)
      $insteadOf[] = $this->pprint($item);

    $fragment = "traitPrecedence(" . $this->pprint($node->trait) . ",\"" . $node->method . "\",[" . implode(",",$insteadOf) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintTryCatchStmt(PHPParser_Node_Stmt_TryCatch $node)
  {
    $finallyBody = array();
    if (null != $node->finallyStmts)
      foreach($node->finallyStmts as $fstmt)
        $finallyBody[] = $this->pprint($fstmt);
        
    $catches = array();
    foreach($node->catches as $toCatch)
      $catches[] = $this->pprint($toCatch);

    $body = array();
    foreach($node->stmts as $stmt)
      $body[] = $this->pprint($stmt);

    if (null != $node->finallyStmts)
      $fragment = "tryCatchFinally([" . implode(",", $body) . "],[" . implode(",",$catches) . "],[" . implode(",",$finallyBody) . "])";
	else
      $fragment = "tryCatch([" . implode(",", $body) . "],[" . implode(",",$catches) . "])";	
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintUnsetStmt(PHPParser_Node_Stmt_Unset $node)
  {
    $vars = array();
    foreach($node->vars as $var)
      $vars[] = $this->pprint($var);

    $fragment = "unset([" . implode(",", $vars) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintUseStmt(PHPParser_Node_Stmt_Use $node)
  {
    $uses = array();
    foreach($node->uses as $use)
      $uses[] = $this->pprint($use);

    $fragment = "use([" . implode(",", $uses) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }

  public function pprintUseUseStmt(PHPParser_Node_Stmt_UseUse $node)
  {
    $name = $this->pprint($node->name);
    if (null != $node->alias)
      $alias = "someName(name(\"" . $node->alias . "\"))";
    else
      $alias = "noName()";

    $fragment = "use(" . $name . "," . $alias . ")";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
  
  public function pprintWhileStmt(PHPParser_Node_Stmt_While $node)
  {
    $stmts = array();
    foreach($node->stmts as $stmt) 
      $stmts[] = $this->pprint($stmt);

    $fragment = "\\while(" . $this->pprint($node->cond) . ",[" . implode(",",$stmts) . "])";
    $fragment .= $this->annotateASTNode($node);

    return $fragment;
  }
}

if (count($argv) < 2) {
  echo "Expected at least 1 argument\n";
  exit -1;
}

$opts = getopt("f:lirp:",array("file:","enableLocations","uniqueIds","relativeLocations","prefix:"));

if (isset($opts["f"]))
	$file = $opts["f"];
else if (isset($opts["file"]))
	$file = $opts["file"];
else if (count($argv) == 2) {
	$file = $argv[1];
} else {
	echo "errscript(\"The file must be provided using either -f or --file\")";
	exit -1;
}

$enableLocations = FALSE;
if (isset($opts["l"]) || isset($opts["enableLocations"]))
	$enableLocations = TRUE;
	
$uniqueIds = FALSE;
if (isset($opts["i"]) || isset($opts["uniqueIds"]))
	$uniqueIds = TRUE;

if (isset($opts["p"]))
	$prefix = $opts["p"].'.';
else if (isset($opts["prefix"]))
	$prefix = $opts["prefix"].'.';
else {
	$prefix = "";
}

$relativeLocations = FALSE;
if (isset($opts["r"]) || isset($opts["relativeLocations"]))
	$relativeLocations = TRUE;

if (isset($_SERVER['HOME'])) {	
	$homedir = $_SERVER['HOME'];
} else {
	$homedir = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
}

$inputCode = '';
if (!$relativeLocations && file_exists($file))
  $inputCode = file_get_contents($file);
else if ($relativeLocations && file_exists($homedir.$file))
  $inputCode = file_get_contents($homedir.$file);
else {
  echo "errscript(\"The given file, $file, does not exist\")";
  exit -1;
}

$parser = new PHPParser_Parser(new PHPParser_Lexer);
$dumper = new PHPParser_NodeDumper;
$printer = new AST2Rascal($file, $enableLocations, $relativeLocations, $uniqueIds, $prefix);

try {
  $stmts = $parser->parse($inputCode);
  $strStmts = array();
  foreach($stmts as $stmt) $strStmts[] = $printer->pprint($stmt);
  $script = implode(",\n", $strStmts);
  echo "script([" . $script . "])";
} catch (PHPParser_Error $e) {
  echo "errscript(\"" . $printer->rascalizeString($e->getMessage()) . "\")";
} catch (Exception $e) {
  echo "errscript(\"" . $printer->rascalizeString($e->getMessage()) . "\")";
}
?>
