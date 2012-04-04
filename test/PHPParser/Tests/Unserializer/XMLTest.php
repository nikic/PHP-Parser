<?php

class PHPParser_Tests_Unserializer_XMLTest extends PHPUnit_Framework_TestCase
{
    public function testNode() {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:node="http://nikic.github.com/PHPParser/XML/node" xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode" xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <node:Scalar_String line="1" docComment="/** doc comment */">
  <subNode:value>
   <scalar:string>Test</scalar:string>
  </subNode:value>
 </node:Scalar_String>
</AST>
XML;

        $unserializer  = new PHPParser_Unserializer_XML;
        $this->assertEquals(
            new PHPParser_Node_Scalar_String('Test', 1, '/** doc comment */'),
            $unserializer->unserialize($xml)
        );
    }

    public function testEmptyNode() {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:node="http://nikic.github.com/PHPParser/XML/node" xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode" xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <node:Scalar_ClassConst />
</AST>
XML;

        $unserializer  = new PHPParser_Unserializer_XML;
        $this->assertEquals(
            new PHPParser_Node_Scalar_ClassConst,
            $unserializer->unserialize($xml)
        );
    }

    public function testScalars() {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <scalar:array>
  <scalar:array></scalar:array>
  <scalar:array/>
  <scalar:string>test</scalar:string>
  <scalar:string></scalar:string>
  <scalar:string/>
  <scalar:int>1</scalar:int>
  <scalar:float>1</scalar:float>
  <scalar:float>1.5</scalar:float>
  <scalar:true/>
  <scalar:false/>
  <scalar:null/>
 </scalar:array>
</AST>
XML;
        $result = array(
            array(), array(),
            'test', '', '',
            1,
            1, 1.5,
            true, false, null
        );

        $unserializer  = new PHPParser_Unserializer_XML;
        $this->assertEquals($result, $unserializer->unserialize($xml));
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage AST root element not found
     */
    public function testWrongRootElementError() {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<notAST/>
XML;

        $unserializer = new PHPParser_Unserializer_XML;
        $unserializer->unserialize($xml);
    }

    /**
     * @dataProvider             provideTestErrors
     */
    public function testErrors($xml, $errorMsg) {
        $this->setExpectedException('DomainException', $errorMsg);

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar"
     xmlns:node="http://nikic.github.com/PHPParser/XML/node"
     xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode"
     xmlns:foo="http://nikic.github.com/PHPParser/XML/foo">
 $xml
</AST>
XML;

        $unserializer = new PHPParser_Unserializer_XML;
        $unserializer->unserialize($xml);
    }

    public function provideTestErrors() {
        return array(
            array('<scalar:true>test</scalar:true>',   '"true" scalar must be empty'),
            array('<scalar:false>test</scalar:false>', '"false" scalar must be empty'),
            array('<scalar:null>test</scalar:null>',   '"null" scalar must be empty'),
            array('<scalar:foo>bar</scalar:foo>',      'Unknown scalar type "foo"'),
            array('<scalar:int>x</scalar:int>',        '"x" is not a valid int'),
            array('<scalar:float>x</scalar:float>',    '"x" is not a valid float'),
            array('',                                  'Expected node or scalar'),
            array('<foo:bar>test</foo:bar>',           'Unexpected node of type "foo:bar"'),
            array(
                '<node:Scalar_String><foo:bar>test</foo:bar></node:Scalar_String>',
                'Expected sub node, got node of type "foo:bar"'
            ),
            array(
                '<node:Scalar_String><subNode:value/></node:Scalar_String>',
                'Expected node or scalar'
            ),
        );
    }
}