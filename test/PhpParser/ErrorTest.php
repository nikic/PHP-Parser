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

    /** @dataProvider provideTestColumnInfo */
    public function testColumnInfo($code, $startPos, $endPos, $startColumn, $endColumn) {
        $error = new Error('Some error', array(
            'startFilePos' => $startPos,
            'endFilePos' => $endPos,
        ));

        $this->assertSame(true, $error->hasColumnInfo());
        $this->assertSame($startColumn, $error->getStartColumn($code));
        $this->assertSame($endColumn, $error->getEndColumn($code));

    }

    public function provideTestColumnInfo() {
        return array(
            // Error at "bar"
            array("<?php foo bar baz", 10, 12, 10, 12),
            array("<?php\nfoo bar baz", 10, 12, 4, 6),
            array("<?php foo\nbar baz", 10, 12, 0, 2),
            array("<?php foo bar\nbaz", 10, 12, 10, 12),
            array("<?php\r\nfoo bar baz", 11, 13, 4, 6),
            // Error at "baz"
            array("<?php foo bar baz", 14, 16, 14, 16),
            array("<?php foo bar\nbaz", 14, 16, 0, 2),
            // Error at string literal
            array("<?php foo 'bar\nbaz' xyz", 10, 18, 10, 3),
            array("<?php\nfoo 'bar\nbaz' xyz", 10, 18, 4, 3),
            array("<?php foo\n'\nbarbaz\n'\nxyz", 10, 19, 0, 0),
            // Error over full string
            array("<?php", 0, 4, 0, 4),
            array("<?\nphp", 0, 5, 0, 2),
        );
    }

    public function testNoColumnInfo(){
        $error = new Error('Some error', 3);

        $this->assertSame(false, $error->hasColumnInfo());
        try {
            $error->getStartColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Error does not have column information', $e->getMessage());
        }
        try {
            $error->getEndColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Error does not have column information', $e->getMessage());
        }
    }
}
