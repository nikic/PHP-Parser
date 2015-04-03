<?php

namespace PhpParser;

/**
 * @codeCoverageIgnore
 */
class Autoloader
{
    /** @var bool Whether the autoloader has been registered. */
    private static $registered = false;

    /** @var bool Whether we're running on PHP 7. */
    private static $runningOnPhp7;

    /**
     * Registers PhpParser\Autoloader as an SPL autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader instead of appending
     */
    static public function register($prepend = false) {
        if (self::$registered === true) {
            return;
        }

        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        self::$registered = true;
        self::$runningOnPhp7 = version_compare(PHP_VERSION, '7.0-dev', '>=');
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    static public function autoload($class) {
        if (0 === strpos($class, 'PhpParser\\')) {
            if (isset(self::$php7AliasesOldToNew[$class])) {
                if (self::$runningOnPhp7) {
                    return;
                }

                // Load the new class, alias will be registered afterwards
                $class = self::$php7AliasesOldToNew[$class];
            }

            $fileName = dirname(__DIR__) . '/' . strtr($class, '\\', '/') . '.php';
            if (file_exists($fileName)) {
                require $fileName;
            }

            if (isset(self::$php7AliasesNewToOld[$class])) {
                // New class name was used, register alias for old one, otherwise
                // it won't be usable in "instanceof" and other non-autoloading places.
                if (!self::$runningOnPhp7) {
                    class_alias($class, self::$php7AliasesNewToOld[$class]);
                }
            }
        } else if (0 === strpos($class, 'PHPParser_')) {
            if (isset(self::$nonNamespacedAliases[$class])) {
                // Register all aliases at once to avoid dependency issues
                self::registerNonNamespacedAliases();
            }
        }
    }

    private static function registerNonNamespacedAliases() {
        foreach (self::$nonNamespacedAliases as $old => $new) {
            class_alias($new, $old);
        }
    }

    private static $php7AliasesOldToNew = array(
        'PhpParser\Node\Expr\Cast\Bool' => 'PhpParser\Node\Expr\Cast\Bool_',
        'PhpParser\Node\Expr\Cast\Int' => 'PhpParser\Node\Expr\Cast\Int_',
        'PhpParser\Node\Expr\Cast\Object' => 'PhpParser\Node\Expr\Cast\Object_',
        'PhpParser\Node\Expr\Cast\String' => 'PhpParser\Node\Expr\Cast\String_',
        'PhpParser\Node\Scalar\String' => 'PhpParser\Node\Scalar\String_',
    );

    private static $php7AliasesNewToOld = array(
        'PhpParser\Node\Expr\Cast\Bool_' => 'PhpParser\Node\Expr\Cast\Bool',
        'PhpParser\Node\Expr\Cast\Int_' => 'PhpParser\Node\Expr\Cast\Int',
        'PhpParser\Node\Expr\Cast\Object_' => 'PhpParser\Node\Expr\Cast\Object',
        'PhpParser\Node\Expr\Cast\String_' => 'PhpParser\Node\Expr\Cast\String',
        'PhpParser\Node\Scalar\String_' => 'PhpParser\Node\Scalar\String',
    );

    private static $nonNamespacedAliases = array(
        'PHPParser_Builder' => 'PhpParser\Builder',
        'PHPParser_BuilderAbstract' => 'PhpParser\BuilderAbstract',
        'PHPParser_BuilderFactory' => 'PhpParser\BuilderFactory',
        'PHPParser_Comment' => 'PhpParser\Comment',
        'PHPParser_Comment_Doc' => 'PhpParser\Comment\Doc',
        'PHPParser_Error' => 'PhpParser\Error',
        'PHPParser_Lexer' => 'PhpParser\Lexer',
        'PHPParser_Lexer_Emulative' => 'PhpParser\Lexer\Emulative',
        'PHPParser_Node' => 'PhpParser\Node',
        'PHPParser_NodeAbstract' => 'PhpParser\NodeAbstract',
        'PHPParser_NodeDumper' => 'PhpParser\NodeDumper',
        'PHPParser_NodeTraverser' => 'PhpParser\NodeTraverser',
        'PHPParser_NodeTraverserInterface' => 'PhpParser\NodeTraverserInterface',
        'PHPParser_NodeVisitor' => 'PhpParser\NodeVisitor',
        'PHPParser_NodeVisitor_NameResolver' => 'PhpParser\NodeVisitor\NameResolver',
        'PHPParser_NodeVisitorAbstract' => 'PhpParser\NodeVisitorAbstract',
        'PHPParser_Parser' => 'PhpParser\Parser',
        'PHPParser_PrettyPrinterAbstract' => 'PhpParser\PrettyPrinterAbstract',
        'PHPParser_PrettyPrinter_Default' => 'PhpParser\PrettyPrinter\Standard',
        'PHPParser_PrettyPrinter_Zend' => 'PhpParser\PrettyPrinter\Standard',
        'PHPParser_Serializer' => 'PhpParser\Serializer',
        'PHPParser_Serializer_XML' => 'PhpParser\Serializer\XML',
        'PHPParser_Unserializer' => 'PhpParser\Unserializer',
        'PHPParser_Unserializer_XML' => 'PhpParser\Unserializer\XML',

        'PHPParser_Builder_Class' => 'PhpParser\Builder\Class_',
        'PHPParser_Builder_Function' => 'PhpParser\Builder\Function_',
        'PHPParser_Builder_Interface' => 'PhpParser\Builder\Interface_',
        'PHPParser_Builder_Method' => 'PhpParser\Builder\Method',
        'PHPParser_Builder_Param' => 'PhpParser\Builder\Param',
        'PHPParser_Builder_Property' => 'PhpParser\Builder\Property',

        'PHPParser_Node_Arg' => 'PhpParser\Node\Arg',
        'PHPParser_Node_Const' => 'PhpParser\Node\Const_',
        'PHPParser_Node_Expr' => 'PhpParser\Node\Expr',
        'PHPParser_Node_Name' => 'PhpParser\Node\Name',
        'PHPParser_Node_Name_FullyQualified' => 'PhpParser\Node\Name\FullyQualified',
        'PHPParser_Node_Name_Relative' => 'PhpParser\Node\Name\Relative',
        'PHPParser_Node_Param' => 'PhpParser\Node\Param',
        'PHPParser_Node_Scalar' => 'PhpParser\Node\Scalar',
        'PHPParser_Node_Stmt' => 'PhpParser\Node\Stmt',

        'PHPParser_Node_Stmt_Break' => 'PhpParser\Node\Stmt\Break_',
        'PHPParser_Node_Stmt_Case' => 'PhpParser\Node\Stmt\Case_',
        'PHPParser_Node_Stmt_Catch' => 'PhpParser\Node\Stmt\Catch_',
        'PHPParser_Node_Stmt_Class' => 'PhpParser\Node\Stmt\Class_',
        'PHPParser_Node_Stmt_ClassConst' => 'PhpParser\Node\Stmt\ClassConst',
        'PHPParser_Node_Stmt_ClassMethod' => 'PhpParser\Node\Stmt\ClassMethod',
        'PHPParser_Node_Stmt_Const' => 'PhpParser\Node\Stmt\Const_',
        'PHPParser_Node_Stmt_Continue' => 'PhpParser\Node\Stmt\Continue_',
        'PHPParser_Node_Stmt_Declare' => 'PhpParser\Node\Stmt\Declare_',
        'PHPParser_Node_Stmt_DeclareDeclare' => 'PhpParser\Node\Stmt\DeclareDeclare',
        'PHPParser_Node_Stmt_Do' => 'PhpParser\Node\Stmt\Do_',
        'PHPParser_Node_Stmt_Echo' => 'PhpParser\Node\Stmt\Echo_',
        'PHPParser_Node_Stmt_Else' => 'PhpParser\Node\Stmt\Else_',
        'PHPParser_Node_Stmt_ElseIf' => 'PhpParser\Node\Stmt\ElseIf_',
        'PHPParser_Node_Stmt_For' => 'PhpParser\Node\Stmt\For_',
        'PHPParser_Node_Stmt_Foreach' => 'PhpParser\Node\Stmt\Foreach_',
        'PHPParser_Node_Stmt_Function' => 'PhpParser\Node\Stmt\Function_',
        'PHPParser_Node_Stmt_Global' => 'PhpParser\Node\Stmt\Global_',
        'PHPParser_Node_Stmt_Goto' => 'PhpParser\Node\Stmt\Goto_',
        'PHPParser_Node_Stmt_HaltCompiler' => 'PhpParser\Node\Stmt\HaltCompiler',
        'PHPParser_Node_Stmt_If' => 'PhpParser\Node\Stmt\If_',
        'PHPParser_Node_Stmt_InlineHTML' => 'PhpParser\Node\Stmt\InlineHTML',
        'PHPParser_Node_Stmt_Interface' => 'PhpParser\Node\Stmt\Interface_',
        'PHPParser_Node_Stmt_Label' => 'PhpParser\Node\Stmt\Label',
        'PHPParser_Node_Stmt_Namespace' => 'PhpParser\Node\Stmt\Namespace_',
        'PHPParser_Node_Stmt_Property' => 'PhpParser\Node\Stmt\Property',
        'PHPParser_Node_Stmt_PropertyProperty' => 'PhpParser\Node\Stmt\PropertyProperty',
        'PHPParser_Node_Stmt_Return' => 'PhpParser\Node\Stmt\Return_',
        'PHPParser_Node_Stmt_Static' => 'PhpParser\Node\Stmt\Static_',
        'PHPParser_Node_Stmt_StaticVar' => 'PhpParser\Node\Stmt\StaticVar',
        'PHPParser_Node_Stmt_Switch' => 'PhpParser\Node\Stmt\Switch_',
        'PHPParser_Node_Stmt_Throw' => 'PhpParser\Node\Stmt\Throw_',
        'PHPParser_Node_Stmt_Trait' => 'PhpParser\Node\Stmt\Trait_',
        'PHPParser_Node_Stmt_TraitUse' => 'PhpParser\Node\Stmt\TraitUse',
        'PHPParser_Node_Stmt_TraitUseAdaptation' => 'PhpParser\Node\Stmt\TraitUseAdaptation',
        'PHPParser_Node_Stmt_TraitUseAdaptation_Alias' => 'PhpParser\Node\Stmt\TraitUseAdaptation\Alias',
        'PHPParser_Node_Stmt_TraitUseAdaptation_Precedence' => 'PhpParser\Node\Stmt\TraitUseAdaptation\Precedence',
        'PHPParser_Node_Stmt_TryCatch' => 'PhpParser\Node\Stmt\TryCatch',
        'PHPParser_Node_Stmt_Unset' => 'PhpParser\Node\Stmt\Unset_',
        'PHPParser_Node_Stmt_UseUse' => 'PhpParser\Node\Stmt\UseUse',
        'PHPParser_Node_Stmt_Use' => 'PhpParser\Node\Stmt\Use_',
        'PHPParser_Node_Stmt_While' => 'PhpParser\Node\Stmt\While_',

        'PHPParser_Node_Expr_AssignBitwiseAnd' => 'PhpParser\Node\Expr\AssignOp\BitwiseAnd',
        'PHPParser_Node_Expr_AssignBitwiseOr' => 'PhpParser\Node\Expr\AssignOp\BitwiseOr',
        'PHPParser_Node_Expr_AssignBitwiseXor' => 'PhpParser\Node\Expr\AssignOp\BitwiseXor',
        'PHPParser_Node_Expr_AssignConcat' => 'PhpParser\Node\Expr\AssignOp\Concat',
        'PHPParser_Node_Expr_AssignDiv' => 'PhpParser\Node\Expr\AssignOp\Div',
        'PHPParser_Node_Expr_AssignMinus' => 'PhpParser\Node\Expr\AssignOp\Minus',
        'PHPParser_Node_Expr_AssignMod' => 'PhpParser\Node\Expr\AssignOp\Mod',
        'PHPParser_Node_Expr_AssignMul' => 'PhpParser\Node\Expr\AssignOp\Mul',
        'PHPParser_Node_Expr_AssignPlus' => 'PhpParser\Node\Expr\AssignOp\Plus',
        'PHPParser_Node_Expr_AssignShiftLeft' => 'PhpParser\Node\Expr\AssignOp\ShiftLeft',
        'PHPParser_Node_Expr_AssignShiftRight' => 'PhpParser\Node\Expr\AssignOp\ShiftRight',

        'PHPParser_Node_Expr_Cast' => 'PhpParser\Node\Expr\Cast',
        'PHPParser_Node_Expr_Cast_Array' => 'PhpParser\Node\Expr\Cast\Array_',
        'PHPParser_Node_Expr_Cast_Bool' => 'PhpParser\Node\Expr\Cast\Bool_',
        'PHPParser_Node_Expr_Cast_Double' => 'PhpParser\Node\Expr\Cast\Double',
        'PHPParser_Node_Expr_Cast_Int' => 'PhpParser\Node\Expr\Cast\Int_',
        'PHPParser_Node_Expr_Cast_Object' => 'PhpParser\Node\Expr\Cast\Object_',
        'PHPParser_Node_Expr_Cast_String' => 'PhpParser\Node\Expr\Cast\String_',
        'PHPParser_Node_Expr_Cast_Unset' => 'PhpParser\Node\Expr\Cast\Unset_',

        'PHPParser_Node_Expr_BitwiseAnd' => 'PhpParser\Node\Expr\BinaryOp\BitwiseAnd',
        'PHPParser_Node_Expr_BitwiseOr' => 'PhpParser\Node\Expr\BinaryOp\BitwiseOr',
        'PHPParser_Node_Expr_BitwiseXor' => 'PhpParser\Node\Expr\BinaryOp\BitwiseXor',
        'PHPParser_Node_Expr_BooleanAnd' => 'PhpParser\Node\Expr\BinaryOp\BooleanAnd',
        'PHPParser_Node_Expr_BooleanOr' => 'PhpParser\Node\Expr\BinaryOp\BooleanOr',
        'PHPParser_Node_Expr_Concat' => 'PhpParser\Node\Expr\BinaryOp\Concat',
        'PHPParser_Node_Expr_Div' => 'PhpParser\Node\Expr\BinaryOp\Div',
        'PHPParser_Node_Expr_Equal' => 'PhpParser\Node\Expr\BinaryOp\Equal',
        'PHPParser_Node_Expr_Greater' => 'PhpParser\Node\Expr\BinaryOp\Greater',
        'PHPParser_Node_Expr_GreaterOrEqual' => 'PhpParser\Node\Expr\BinaryOp\GreaterOrEqual',
        'PHPParser_Node_Expr_Identical' => 'PhpParser\Node\Expr\BinaryOp\Identical',
        'PHPParser_Node_Expr_LogicalAnd' => 'PhpParser\Node\Expr\BinaryOp\LogicalAnd',
        'PHPParser_Node_Expr_LogicalOr' => 'PhpParser\Node\Expr\BinaryOp\LogicalOr',
        'PHPParser_Node_Expr_LogicalXor' => 'PhpParser\Node\Expr\BinaryOp\LogicalXor',
        'PHPParser_Node_Expr_Minus' => 'PhpParser\Node\Expr\BinaryOp\Minus',
        'PHPParser_Node_Expr_Mod' => 'PhpParser\Node\Expr\BinaryOp\Mod',
        'PHPParser_Node_Expr_Mul' => 'PhpParser\Node\Expr\BinaryOp\Mul',
        'PHPParser_Node_Expr_NotEqual' => 'PhpParser\Node\Expr\BinaryOp\NotEqual',
        'PHPParser_Node_Expr_NotIdentical' => 'PhpParser\Node\Expr\BinaryOp\NotIdentical',
        'PHPParser_Node_Expr_Plus' => 'PhpParser\Node\Expr\BinaryOp\Plus',
        'PHPParser_Node_Expr_ShiftLeft' => 'PhpParser\Node\Expr\BinaryOp\ShiftLeft',
        'PHPParser_Node_Expr_ShiftRight' => 'PhpParser\Node\Expr\BinaryOp\ShiftRight',
        'PHPParser_Node_Expr_Smaller' => 'PhpParser\Node\Expr\BinaryOp\Smaller',
        'PHPParser_Node_Expr_SmallerOrEqual' => 'PhpParser\Node\Expr\BinaryOp\SmallerOrEqual',

        'PHPParser_Node_Expr_Array' => 'PhpParser\Node\Expr\Array_',
        'PHPParser_Node_Expr_ArrayDimFetch' => 'PhpParser\Node\Expr\ArrayDimFetch',
        'PHPParser_Node_Expr_ArrayItem' => 'PhpParser\Node\Expr\ArrayItem',
        'PHPParser_Node_Expr_Assign' => 'PhpParser\Node\Expr\Assign',
        'PHPParser_Node_Expr_AssignRef' => 'PhpParser\Node\Expr\AssignRef',
        'PHPParser_Node_Expr_BitwiseNot' => 'PhpParser\Node\Expr\BitwiseNot',
        'PHPParser_Node_Expr_BooleanNot' => 'PhpParser\Node\Expr\BooleanNot',
        'PHPParser_Node_Expr_ClassConstFetch' => 'PhpParser\Node\Expr\ClassConstFetch',
        'PHPParser_Node_Expr_Clone' => 'PhpParser\Node\Expr\Clone_',
        'PHPParser_Node_Expr_Closure' => 'PhpParser\Node\Expr\Closure',
        'PHPParser_Node_Expr_ClosureUse' => 'PhpParser\Node\Expr\ClosureUse',
        'PHPParser_Node_Expr_ConstFetch' => 'PhpParser\Node\Expr\ConstFetch',
        'PHPParser_Node_Expr_Empty' => 'PhpParser\Node\Expr\Empty_',
        'PHPParser_Node_Expr_ErrorSuppress' => 'PhpParser\Node\Expr\ErrorSuppress',
        'PHPParser_Node_Expr_Eval' => 'PhpParser\Node\Expr\Eval_',
        'PHPParser_Node_Expr_Exit' => 'PhpParser\Node\Expr\Exit_',
        'PHPParser_Node_Expr_FuncCall' => 'PhpParser\Node\Expr\FuncCall',
        'PHPParser_Node_Expr_Include' => 'PhpParser\Node\Expr\Include_',
        'PHPParser_Node_Expr_Instanceof' => 'PhpParser\Node\Expr\Instanceof_',
        'PHPParser_Node_Expr_Isset' => 'PhpParser\Node\Expr\Isset_',
        'PHPParser_Node_Expr_List' => 'PhpParser\Node\Expr\List_',
        'PHPParser_Node_Expr_MethodCall' => 'PhpParser\Node\Expr\MethodCall',
        'PHPParser_Node_Expr_New' => 'PhpParser\Node\Expr\New_',
        'PHPParser_Node_Expr_PostDec' => 'PhpParser\Node\Expr\PostDec',
        'PHPParser_Node_Expr_PostInc' => 'PhpParser\Node\Expr\PostInc',
        'PHPParser_Node_Expr_PreDec' => 'PhpParser\Node\Expr\PreDec',
        'PHPParser_Node_Expr_PreInc' => 'PhpParser\Node\Expr\PreInc',
        'PHPParser_Node_Expr_Print' => 'PhpParser\Node\Expr\Print_',
        'PHPParser_Node_Expr_PropertyFetch' => 'PhpParser\Node\Expr\PropertyFetch',
        'PHPParser_Node_Expr_ShellExec' => 'PhpParser\Node\Expr\ShellExec',
        'PHPParser_Node_Expr_StaticCall' => 'PhpParser\Node\Expr\StaticCall',
        'PHPParser_Node_Expr_StaticPropertyFetch' => 'PhpParser\Node\Expr\StaticPropertyFetch',
        'PHPParser_Node_Expr_Ternary' => 'PhpParser\Node\Expr\Ternary',
        'PHPParser_Node_Expr_UnaryMinus' => 'PhpParser\Node\Expr\UnaryMinus',
        'PHPParser_Node_Expr_UnaryPlus' => 'PhpParser\Node\Expr\UnaryPlus',
        'PHPParser_Node_Expr_Variable' => 'PhpParser\Node\Expr\Variable',
        'PHPParser_Node_Expr_Yield' => 'PhpParser\Node\Expr\Yield_',

        'PHPParser_Node_Scalar_ClassConst' => 'PhpParser\Node\Scalar\MagicConst\Class_',
        'PHPParser_Node_Scalar_DirConst' => 'PhpParser\Node\Scalar\MagicConst\Dir',
        'PHPParser_Node_Scalar_FileConst' => 'PhpParser\Node\Scalar\MagicConst\File',
        'PHPParser_Node_Scalar_FuncConst' => 'PhpParser\Node\Scalar\MagicConst\Function_',
        'PHPParser_Node_Scalar_LineConst' => 'PhpParser\Node\Scalar\MagicConst\Line',
        'PHPParser_Node_Scalar_MethodConst' => 'PhpParser\Node\Scalar\MagicConst\Method',
        'PHPParser_Node_Scalar_NSConst' => 'PhpParser\Node\Scalar\MagicConst\Namespace_',
        'PHPParser_Node_Scalar_TraitConst' => 'PhpParser\Node\Scalar\MagicConst\Trait_',

        'PHPParser_Node_Scalar_DNumber' => 'PhpParser\Node\Scalar\DNumber',
        'PHPParser_Node_Scalar_Encapsed' => 'PhpParser\Node\Scalar\Encapsed',
        'PHPParser_Node_Scalar_LNumber' => 'PhpParser\Node\Scalar\LNumber',
        'PHPParser_Node_Scalar_String' => 'PhpParser\Node\Scalar\String_',
    );
}

class_alias('PhpParser\Autoloader', 'PHPParser_Autoloader');
