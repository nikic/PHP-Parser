<?php

class PHPParser_Tests_Unserializer_XMLTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PHPParser_Unserializer_XML<extended>
     */
    public function testUnserialize() {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:node="http://nikic.github.com/PHPParser/XML/node" xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode" xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <scalar:array>
  <node:Stmt_Function line="2">
   <subNode:byRef>
    <scalar:false/>
   </subNode:byRef>
   <subNode:params>
    <scalar:array>
     <node:Param line="2">
      <subNode:name>
       <scalar:string>b</scalar:string>
      </subNode:name>
      <subNode:default>
       <node:Expr_ConstFetch line="2">
        <subNode:name>
         <node:Name line="2">
          <subNode:parts>
           <scalar:array>
            <scalar:string>C</scalar:string>
           </scalar:array>
          </subNode:parts>
         </node:Name>
        </subNode:name>
       </node:Expr_ConstFetch>
      </subNode:default>
      <subNode:type>
       <scalar:null/>
      </subNode:type>
      <subNode:byRef>
       <scalar:false/>
      </subNode:byRef>
     </node:Param>
    </scalar:array>
   </subNode:params>
   <subNode:stmts>
    <scalar:array>
     <node:Stmt_Echo line="3">
      <subNode:exprs>
       <scalar:array>
        <node:Scalar_String line="3">
         <subNode:value>
          <scalar:string>Foo</scalar:string>
         </subNode:value>
        </node:Scalar_String>
        <node:Scalar_String line="3">
         <subNode:value>
          <scalar:string>Bar</scalar:string>
         </subNode:value>
        </node:Scalar_String>
       </scalar:array>
      </subNode:exprs>
     </node:Stmt_Echo>
    </scalar:array>
   </subNode:stmts>
   <subNode:name>
    <scalar:string>A</scalar:string>
   </subNode:name>
  </node:Stmt_Function>
 </scalar:array>
</AST>
XML;
        $code = <<<'CODE'
function A($b = C)
{
    echo 'Foo', 'Bar';
}
CODE;

        $unserializer  = new PHPParser_Unserializer_XML;
        $prettyPrinter = new PHPParser_PrettyPrinter_Zend;

        $stmts = $unserializer->unserialize($xml);
        $this->assertEquals($code, $prettyPrinter->prettyPrint($stmts), '', 0, 10, true);
    }
}