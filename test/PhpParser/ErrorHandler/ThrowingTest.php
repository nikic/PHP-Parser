<?php declare(strict_types=1);

namespace PhpParser\ErrorHandler;

use PhpParser\Error;

class ThrowingTest extends \PHPUnit\Framework\TestCase {
    public function testHandleError(): void {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Test');
        $errorHandler = new Throwing();
        $errorHandler->handleError(new Error('Test'));
    }
}
