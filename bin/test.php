<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpParser\Error;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

$code = <<<'CODE'
<?php

namespace App;

use PhpParser\ParserFactory;

class Test<T,V> extends GenericClass<T> implements GenericInterface<V> {
 
  use GenericTrait<T>;
  use T;
 
  private T|GenericClass<V> $var;
  private $var2;
  
  private readonly (T&V)|null|false|true $content = null;

  public function setContent((T&V)|null|false|true $content): (T&V)|null|false|true {

  }
 
  public function test(T|GenericInterface<V> $var): T|GenericClass<V> {
      
       var_dump($var instanceof GenericInterface<V>);
      
       var_dump($var instanceof T);
      
       var_dump(GenericClass<T>::class);
      
       var_dump(T::class);
      
       var_dump(GenericClass<T>::CONSTANT);
      
       var_dump(T::CONSTANT);
      
       $obj1 = new T();
       $obj2 = new GenericClass<V>();
      
       return $obj2;
  }
}
CODE;

$lexer  = new Emulative();
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
try {
    $ast = $parser->parse($code);
} catch (Error $error) {
    echo "Error: {$error->getMessage()}\n";
    exit(1);
}

$dumper = new NodeDumper(['dumpGenerics' => true]);
if (trim(file_get_contents(__DIR__ . '/test.output')) !== $dumper->dump($ast)) {
    print("Failed!\n");
    exit(1);
}

print("Success!\n");
