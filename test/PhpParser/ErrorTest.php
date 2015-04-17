<?php

namespace PhpParser;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $error = new Error('Some error', 10);

        $this->assertSame('Some error', $error->getRawMessage());
        $this->assertSame(10, $error->getRawLine());
        $this->assertSame('Some error on line 10', $error->getMessage());

        return $error;
    }

    /**
     * @depends testConstruct
     */
    public function testSetMessageAndLine(Error $error) {
        $error->setRawMessage('Some other error');
        $error->setRawLine(15);

        $this->assertSame('Some other error', $error->getRawMessage());
        $this->assertSame(15, $error->getRawLine());
        $this->assertSame('Some other error on line 15', $error->getMessage());
    }

    public function testUnknownLine() {
        $error = new Error('Some error');

        $this->assertSame(-1, $error->getRawLine());
        $this->assertSame('Some error on unknown line', $error->getMessage());
    }

    /**
     * @depends testConstruct
     */
    public function testColumnNumbers() {

        $faultyCode = "<?php \$foo = bar baz; ?>";

        $tokens = token_get_all($faultyCode);

        $error = new Error('Some error', 1, $tokens, 5);

        $this->assertSame(true, $error->hasTokenAttributes());
        $this->assertSame(13, $error->getBeginColumn());
        $this->assertSame(16, $error->getEndColumn());

    }

    /**
     * @depends testConstruct
     */
    public function testTokenInformationMissing(){

        $error = new Error('Some error', 3);

        $this->assertSame(false, $error->hasTokenAttributes());
        $this->assertSame(null, $error->getBeginColumn());
        $this->assertSame(null, $error->getEndColumn());
    }
}
