<?php

namespace PhpParser;

class TemplateLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithoutSuffix() {
        $templateLoader = new TemplateLoader(
            new Parser(new Lexer),
            __DIR__
        );

        // load this file as a template, as we don't really care about the contents
        $template = $templateLoader->load('TemplateLoaderTest.php');
        $this->assertInstanceOf('PhpParser\Template', $template);
    }

    public function testLoadWithSuffix() {
        $templateLoader = new TemplateLoader(
            new Parser(new Lexer),
            __DIR__, '.php'
        );

        // load this file as a template, as we don't really care about the contents
        $template = $templateLoader->load('TemplateLoaderTest');
        $this->assertInstanceOf('PhpParser\Template', $template);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonexistentBaseDirectoryError() {
        new TemplateLoader(
            new Parser(new Lexer),
            __DIR__ . '/someDirectoryThatDoesNotExist'
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonexistentFileError() {
        $templateLoader = new TemplateLoader(
            new Parser(new Lexer),
            __DIR__
        );

        $templateLoader->load('SomeTemplateThatDoesNotExist');
    }
}