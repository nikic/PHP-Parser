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
}
