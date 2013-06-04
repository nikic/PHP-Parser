<?php
require '../bootstrap.php';
ini_set('xdebug.max_nesting_level', 2000);

$classNames = array();
$abstractClassNames = array();

class GenerateCode extends PHPParser_NodeVisitorAbstract
{
  public function enterNode(PHPParser_Node $node) {
    global $classNames;
    global $abstractClassNames;
    if ($node instanceof PHPParser_Node_Stmt_Class) {
      if ($node->isAbstract) {
	    array_push($abstractClassNames,$node->name);
      } else if ($node->name !== "PHPParser_Node_Expr") {
      	// See below for why this is filtered...
	    array_push($classNames,$node->name);
      }
    }
  }
}

$parser = new PHPParser_Parser(new PHPParser_Lexer);
$visitor = new PHPParser_NodeTraverser;
$rvis = new GenerateCode;
$visitor->addVisitor($rvis);
$dumper = new PHPParser_NodeDumper;

$startdir = '../PHPParser/Node';

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($startdir), RecursiveIteratorIterator::CHILD_FIRST & RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
  if (!preg_match('~\.php$~', $file)) {
    continue;
  }

  $inputCode = file_get_contents($file);

  try {
    $stmts = $parser->parse($inputCode);
    $stmts = $visitor->traverse($stmts);
  } catch (PHPParser_Error $e) {
    echo 'Parse Error: ', $e->getMessage();
  }
}

$enterNodeCalls = "\tpublic function enterNode(PHPParser_Node \$node)\n\t{\n";
$leaveNodeCalls = "\tpublic function leaveNode(PHPParser_Node \$node)\n\t{\n";
$printNodeCalls = "\tpublic function pprint(PHPParser_Node \$node)\n\t{\n";
$defaultEnters = "";
$defaultLeaves = "";
$defaultPrints = "";
$ifcEnters = "";
$ifcLeaves = "";
$ifcPrints = "\tpublic function pprint(PHPParser_Node \$node);\n";

$firstPass = true;

// TODO: This is not elegant, but, since Scalar extends Expr, these are going
// in the file in the wrong order. This just forces PHPParser_Node_Expr to be
// the last class handled. The better fix would be to compute the inherits
// relation and base the order on this instead. 
array_push($classNames, "PHPParser_Node_Expr");

foreach($classNames as $className) {
  $callName = $className;
  if (!(FALSE === strpos($className,'PHPParser_Node_'))) {
    $callName = substr($className,strlen('PHPParser_Node_'));
    if (!(FALSE === strpos($callName,'_'))) {
      $nameParts = strtok(strrev($callName), "_");
      $callName = "";
      while ($nameParts != FALSE) {
	$callName .= strrev($nameParts);
	$nameParts = strtok("_");
      }
    }
  }
  if ($firstPass) {
    $enterNodeCalls .= "\t\tif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->enter{$callName}(\$node);\n\t\t}";
    $leaveNodeCalls .= "\t\tif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->leave{$callName}(\$node);\n\t\t}";
    $printNodeCalls .= "\t\tif (\$node instanceof {$className}) {\n\t\t\treturn \$this->pprint{$callName}(\$node);\n\t\t}";
    $firstPass = false;
  } else {
    $enterNodeCalls .= " elseif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->enter{$callName}(\$node);\n\t\t}";
    $leaveNodeCalls .= " elseif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->leave{$callName}(\$node);\n\t\t}";
    $printNodeCalls .= " elseif (\$node instanceof {$className}) {\n\t\t\treturn \$this->pprint{$callName}(\$node);\n\t\t}";
  }

  $ifcEnters .= "\tpublic function enter{$callName}({$className} \$node);\n";
  $ifcLeaves .= "\tpublic function leave{$callName}({$className} \$node);\n";
  $ifcPrints .= "\tpublic function pprint{$callName}({$className} \$node);\n";

  $defaultEnters .= "\tpublic function enter{$callName}({$className} \$node)\n\t{\n\t\treturn null;\n\t}\n";
  $defaultLeaves .= "\tpublic function leave{$callName}({$className} \$node)\n\t{\n\t\treturn null;\n\t}\n";
  $defaultPrints .= "\tpublic function pprint{$callName}({$className} \$node)\n\t{\n\t\treturn \"\";\n\t}\n";
}

$enterNodeCalls .= "\n\t}";
$leaveNodeCalls .= "\n\t}";
$printNodeCalls .= "\n\t}";

$usefulProp = "\tprivate \$visitor = null;\n";
$usefulConstructor = "\tpublic function UsefulVisitor(IVisitor \$v)\n\t{\n\t\t\$this->visitor = \$v;\n\t}\n";
$baseVisitorCode = "<?php\nrequire_once('IVisitor.php');\n\nclass UsefulVisitor extends PHPParser_NodeVisitorAbstract\n{\n{$usefulProp}\n{$usefulConstructor}\n{$enterNodeCalls}\n{$leaveNodeCalls}\n}\n?>";
$usefulVisitorInterface = "<?php\ninterface IVisitor\n{\n{$ifcEnters}\n{$ifcLeaves}\n}\n?>";
$usefulPrinterInterface = "<?php\ninterface IPrinter\n{\n{$ifcPrints}\n}\n?>";
$usefulBaseVisitor = "<?php\nclass BaseVisitor implements IVisitor\n{\n{$defaultEnters}\n{$defaultLeaves}\n}\n?>";
$usefulBasePrinter = "<?php\nclass BasePrinter implements IPrinter\n{\n{$printNodeCalls}\n{$defaultPrints}\n}\n?>";

file_put_contents("UsefulVisitor.php", $baseVisitorCode);
file_put_contents("IVisitor.php", $usefulVisitorInterface);
file_put_contents("IPrinter.php", $usefulPrinterInterface);
file_put_contents("BaseVisitor.php", $usefulBaseVisitor);
file_put_contents("BasePrinter.php", $usefulBasePrinter);