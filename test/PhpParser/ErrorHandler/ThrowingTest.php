<?php declare(strict_types=1);

namespace PhpParser\ErrorHandler;

use PhpParser\Error;
use PHPUnit\Framework\TestCase;

class ThrowingTest extends TestCase
{
    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Test
     */
    public function testHandleError() {
        $errorHandler = new Throwing();
        $errorHandler->handleError(new Error('Test'));
    }
}
