<?php

class PHPParser_Tests_CommentTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetText() {
        $comment = new PHPParser_Comment('/* Some comment */');

        $this->assertEquals('/* Some comment */', $comment->getText());
        $this->assertEquals('/* Some comment */', (string) $comment);

        $comment->setText('/* Some other comment */');

        $this->assertEquals('/* Some other comment */', $comment->getText());
        $this->assertEquals('/* Some other comment */', (string) $comment);
    }

    /**
     * @dataProvider provideTestReformatting
     */
    public function testReformatting($commentText, $reformattedText) {
        $comment = new PHPParser_Comment($commentText);
        $this->assertEquals($reformattedText, $comment->getReformattedText());
    }

    public function provideTestReformatting() {
        return array(
            array('// Some text' . "\n", '// Some text'),
            array('/* Some text */', '/* Some text */'),
            array(
                '/**
     * Some text.
     * Some more text.
     */',
                '/**
 * Some text.
 * Some more text.
 */'
            ),
            array(
                '/*
        Some text.
        Some more text.
    */',
                '/*
    Some text.
    Some more text.
*/'
            ),
            array(
                '/* Some text.
       More text.
       Even more text. */',
                '/* Some text.
   More text.
   Even more text. */'
            ),
            // invalid comment -> no reformatting
            array(
                'hallo
    world',
                'hallo
    world',
            ),
        );
    }
}