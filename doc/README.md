Table of Contents
=================

Guide
-----

  1. [Introduction](0_Introduction.markdown)
  2. [Usage of basic components](2_Usage_of_basic_components.markdown)
  3. [Other node tree representations](3_Other_node_tree_representations.markdown)
  4. [Code generation](4_Code_generation.markdown)
  5. [Frequently asked questions](5_FAQ.markdown)
 
Component documentation
-----------------------
 
  * [Name resolution](component/Name_resolution.markdown)
    * Name resolver options
    * Name resolution context
  * [Pretty printing](component/Pretty_printing.markdown)
    * Converting AST back to PHP code
    * Customizing formatting
    * Formatting-preserving code transformations
  * [Lexer](component/Lexer.markdown)
    * Lexer options
    * Token and file positions for nodes
    * Custom attributes
  * [Error handling](component/Error_handling.markdown)
    * Column information for errors
    * Error recovery (parsing of syntactically incorrect code)
  * [Performance](component/Performance.markdown)
    * Disabling XDebug
    * Reusing objects
    * Garbage collection impact
