<?php
require '../PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$classNames = array();

class GenerateCode extends PHPParser_NodeVisitorAbstract
{
  public function enterNode(PHPParser_Node $node) {
    global $classNames;
    if ($node instanceof PHPParser_Node_Stmt_Class) {
      if (! ($node->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT)) {
	array_push($classNames,$node->name);
      }
    }
  }
}

$parser = new PHPParser_Parser;
$visitor = new PHPParser_NodeTraverser;
$rvis = new GenerateCode;
$visitor->addVisitor($rvis);
$dumper = new PHPParser_NodeDumper;

$startdir = '../PHPParser/Node';

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($startdir), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
  if (!preg_match('~\.php$~', $file)) {
    continue;
  }

  $inputCode = file_get_contents($file);
  try {
    $stmts = $parser->parse(new PHPParser_Lexer($inputCode));
    $stmts = $visitor->traverse($stmts);
  } catch (PHPParser_Error $e) {
    echo 'Parse Error: ', $e->getMessage();
  }
}

$enterNodeCalls = "\tpublic function enterNode(PHPParser_Node \$node)\n\t{\n";
$leaveNodeCalls = "\tpublic function leaveNode(PHPParser_Node \$node)\n\t{\n";
$defaultEnters = "";
$defaultLeaves = "";
$ifcEnters = "";
$ifcLeaves = "";

$firstPass = true;

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
    $firstPass = false;
  } else {
    $enterNodeCalls .= " elseif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->enter{$callName}(\$node);\n\t\t}";
    $leaveNodeCalls .= " elseif (\$node instanceof {$className}) {\n\t\t\treturn \$this->visitor->leave{$callName}(\$node);\n\t\t}";
  }

  $ifcEnters .= "\tpublic function enter{$callName}({$className} \$node);\n";
  $ifcLeaves .= "\tpublic function leave{$callName}({$className} \$node);\n";
  $defaultEnters .= "\tpublic function enter{$callName}({$className} \$node)\n\t{\n\t\treturn null;\n\t}\n";
  $defaultLeaves .= "\tpublic function leave{$callName}({$className} \$node)\n\t{\n\t\treturn null;\n\t}\n";
}

$enterNodeCalls .= "\n\t}";
$leaveNodeCalls .= "\n\t}";

$usefulProp = "\tprivate \$visitor = null;\n";
$usefulConstructor = "\tpublic function UsefulVisitor(IVisitor \$v)\n\t{\n\t\t\$this->visitor = \$v;\n\t}\n";
$baseVisitorCode = "<?php\nrequire_once('IVisitor.php');\n\nclass UsefulVisitor extends PHPParser_NodeVisitorAbstract\n{\n{$usefulProp}\n{$usefulConstructor}\n{$enterNodeCalls}\n{$leaveNodeCalls}\n}\n?>";
$usefulVisitorInterface = "<?php\ninterface IVisitor\n{\n{$ifcEnters}\n{$ifcLeaves}\n}\n?>";
$usefulBaseVisitor = "<?php\nclass BaseVisitor implements IVisitor\n{\n{$defaultEnters}\n{$defaultLeaves}\n}\n?>";

file_put_contents("UsefulVisitor.php", $baseVisitorCode);
file_put_contents("IVisitor.php", $usefulVisitorInterface);
file_put_contents("BaseVisitor.php", $usefulBaseVisitor);