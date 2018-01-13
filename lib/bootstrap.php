<?php declare(strict_types=1);

if (!class_exists(PhpParser\Autoloader::class)) {
    require __DIR__ . '/PhpParser/Autoloader.php';
}
PhpParser\Autoloader::register();
