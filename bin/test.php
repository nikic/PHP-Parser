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
    echo "Parse error: {$error->getMessage()}\n";

    return;
}

$dumper = new NodeDumper;
echo $dumper->dump($ast) . "\n";

echo 'SUCCESS!' . "\n";
