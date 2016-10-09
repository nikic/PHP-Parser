<?php

namespace PhpParser\ErrorHandler;

use PhpParser\Error;

class ThrowingTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Test
     */
    public function testHandleError() {
        $errorHandler = new Throwing();
        $errorHandler->handleError(new Error('Test'));
    }
}