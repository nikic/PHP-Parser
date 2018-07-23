## Expr

Class | Node
----- | -----
`PhpParser\Node\Expr\BooleanNot` | `!` operator
`PhpParser\Node\Expr\Empty_` | `empty()` function
`PhpParser\Node\Expr\Exit_` | `exit` function
`PhpParser\Node\Expr\Instanceof_` | `instanceof` operator
`PhpParser\Node\Expr\Isset_` | `isset` operator
`PhpParser\Node\Expr\List_` | `list()` language constructor. `[]` syntax is also handled by this node
`PhpParser\Node\Expr\New_` | `new` keyword

## Expr\Cast

Class | Node
----- | -----
`PhpParser\Node\Expr\Cast\Array_` | `(array)`
`PhpParser\Node\Expr\Cast\Bool_` | `(bool)`
`PhpParser\Node\Expr\Cast\Double` | `(double)`
`PhpParser\Node\Expr\Cast\Int_` | `(int)`
`PhpParser\Node\Expr\Cast\Object_` | `(object)`
`PhpParser\Node\Expr\Cast\String_` | `(string)`
`PhpParser\Node\Expr\Cast\Unset_` | `(unset)`

## Expr\BinaryOp

Class | Node
----- | -----
`PhpParser\Node\Expr\BinaryOp\BitwiseAnd` | `&`
`PhpParser\Node\Expr\BinaryOp\BitwiseOr` | <code>&#124;</code>
`PhpParser\Node\Expr\BinaryOp\BitwiseXor` | `^`
`PhpParser\Node\Expr\BinaryOp\BooleanAnd` | `&&`
`PhpParser\Node\Expr\BinaryOp\BooleanOr` | <code>&#124;&#124;</code>
`PhpParser\Node\Expr\BinaryOp\Coalesce` | `??`
`PhpParser\Node\Expr\BinaryOp\Concat` | `.`
`PhpParser\Node\Expr\BinaryOp\Div` | `/`
`PhpParser\Node\Expr\BinaryOp\Equal` | `==`
`PhpParser\Node\Expr\BinaryOp\Greater` | `>`
`PhpParser\Node\Expr\BinaryOp\GreaterOrEqual` | `>=`
`PhpParser\Node\Expr\BinaryOp\Identical` | `===`
`PhpParser\Node\Expr\BinaryOp\LogicalAnd` | `and`
`PhpParser\Node\Expr\BinaryOp\LogicalOr` | `or`
`PhpParser\Node\Expr\BinaryOp\LogicalXor` | `xor`
`PhpParser\Node\Expr\BinaryOp\Minus` | `-`
`PhpParser\Node\Expr\BinaryOp\Mod` | `%`
`PhpParser\Node\Expr\BinaryOp\Mul` | `*`
`PhpParser\Node\Expr\BinaryOp\NotEqual` | `!=` and `<>`
`PhpParser\Node\Expr\BinaryOp\NotIdentical` | `!==`
`PhpParser\Node\Expr\BinaryOp\Plus` | `+`
`PhpParser\Node\Expr\BinaryOp\Pow` | `**`
`PhpParser\Node\Expr\BinaryOp\ShiftLeft` | `<<`
`PhpParser\Node\Expr\BinaryOp\ShiftRight` | `>>`
`PhpParser\Node\Expr\BinaryOp\Smaller` | `<`
`PhpParser\Node\Expr\BinaryOp\SmallerOrEqual` | `<=`
`PhpParser\Node\Expr\BinaryOp\Spaceship` | `<=>`
