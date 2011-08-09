<?php

/* Prototype file of classed PHP parser.
 * Written by Moriyoshi Koizumi, based on the work by Masato Bito.
 * This file is PUBLIC DOMAIN.
 */
class PHPParser_ParserDebug
{
    const YYBADCH      = 145;
    const YYMAXLEX     = 380;
    const YYTERMS      = 145;
    const YYNONTERMS   = 89;
    const YYLAST       = 948;
    const YY2TBLSTATE  = 337;
    const YYGLAST      = 412;
    const YYSTATES     = 755;
    const YYNLSTATES   = 536;
    const YYINTERRTOK  = 1;
    const YYUNEXPECTED = 32767;
    const YYDEFAULT    = -32766;

    // {{{ Tokens
    const YYERRTOK = 256;
    const T_INCLUDE = 257;
    const T_INCLUDE_ONCE = 258;
    const T_EVAL = 259;
    const T_REQUIRE = 260;
    const T_REQUIRE_ONCE = 261;
    const T_LOGICAL_OR = 262;
    const T_LOGICAL_XOR = 263;
    const T_LOGICAL_AND = 264;
    const T_PRINT = 265;
    const T_PLUS_EQUAL = 266;
    const T_MINUS_EQUAL = 267;
    const T_MUL_EQUAL = 268;
    const T_DIV_EQUAL = 269;
    const T_CONCAT_EQUAL = 270;
    const T_MOD_EQUAL = 271;
    const T_AND_EQUAL = 272;
    const T_OR_EQUAL = 273;
    const T_XOR_EQUAL = 274;
    const T_SL_EQUAL = 275;
    const T_SR_EQUAL = 276;
    const T_BOOLEAN_OR = 277;
    const T_BOOLEAN_AND = 278;
    const T_IS_EQUAL = 279;
    const T_IS_NOT_EQUAL = 280;
    const T_IS_IDENTICAL = 281;
    const T_IS_NOT_IDENTICAL = 282;
    const T_IS_SMALLER_OR_EQUAL = 283;
    const T_IS_GREATER_OR_EQUAL = 284;
    const T_SL = 285;
    const T_SR = 286;
    const T_INSTANCEOF = 287;
    const T_INC = 288;
    const T_DEC = 289;
    const T_INT_CAST = 290;
    const T_DOUBLE_CAST = 291;
    const T_STRING_CAST = 292;
    const T_ARRAY_CAST = 293;
    const T_OBJECT_CAST = 294;
    const T_BOOL_CAST = 295;
    const T_UNSET_CAST = 296;
    const T_NEW = 297;
    const T_CLONE = 298;
    const T_EXIT = 299;
    const T_IF = 300;
    const T_ELSEIF = 301;
    const T_ELSE = 302;
    const T_ENDIF = 303;
    const T_LNUMBER = 304;
    const T_DNUMBER = 305;
    const T_STRING = 306;
    const T_STRING_VARNAME = 307;
    const T_VARIABLE = 308;
    const T_NUM_STRING = 309;
    const T_INLINE_HTML = 310;
    const T_CHARACTER = 311;
    const T_BAD_CHARACTER = 312;
    const T_ENCAPSED_AND_WHITESPACE = 313;
    const T_CONSTANT_ENCAPSED_STRING = 314;
    const T_ECHO = 315;
    const T_DO = 316;
    const T_WHILE = 317;
    const T_ENDWHILE = 318;
    const T_FOR = 319;
    const T_ENDFOR = 320;
    const T_FOREACH = 321;
    const T_ENDFOREACH = 322;
    const T_DECLARE = 323;
    const T_ENDDECLARE = 324;
    const T_AS = 325;
    const T_SWITCH = 326;
    const T_ENDSWITCH = 327;
    const T_CASE = 328;
    const T_DEFAULT = 329;
    const T_BREAK = 330;
    const T_CONTINUE = 331;
    const T_GOTO = 332;
    const T_FUNCTION = 333;
    const T_CONST = 334;
    const T_RETURN = 335;
    const T_TRY = 336;
    const T_CATCH = 337;
    const T_THROW = 338;
    const T_USE = 339;
    const T_GLOBAL = 340;
    const T_STATIC = 341;
    const T_ABSTRACT = 342;
    const T_FINAL = 343;
    const T_PRIVATE = 344;
    const T_PROTECTED = 345;
    const T_PUBLIC = 346;
    const T_VAR = 347;
    const T_UNSET = 348;
    const T_ISSET = 349;
    const T_EMPTY = 350;
    const T_HALT_COMPILER = 351;
    const T_CLASS = 352;
    const T_INTERFACE = 353;
    const T_EXTENDS = 354;
    const T_IMPLEMENTS = 355;
    const T_OBJECT_OPERATOR = 356;
    const T_DOUBLE_ARROW = 357;
    const T_LIST = 358;
    const T_ARRAY = 359;
    const T_CLASS_C = 360;
    const T_METHOD_C = 361;
    const T_FUNC_C = 362;
    const T_LINE = 363;
    const T_FILE = 364;
    const T_COMMENT = 365;
    const T_DOC_COMMENT = 366;
    const T_OPEN_TAG = 367;
    const T_OPEN_TAG_WITH_ECHO = 368;
    const T_CLOSE_TAG = 369;
    const T_WHITESPACE = 370;
    const T_START_HEREDOC = 371;
    const T_END_HEREDOC = 372;
    const T_DOLLAR_OPEN_CURLY_BRACES = 373;
    const T_CURLY_OPEN = 374;
    const T_PAAMAYIM_NEKUDOTAYIM = 375;
    const T_NAMESPACE = 376;
    const T_NS_C = 377;
    const T_DIR = 378;
    const T_NS_SEPARATOR = 379;
    // }}}

    private static $yyterminals = array(
        '$EOF',
        "error",
        "T_INCLUDE",
        "T_INCLUDE_ONCE",
        "T_EVAL",
        "T_REQUIRE",
        "T_REQUIRE_ONCE",
        "','",
        "T_LOGICAL_OR",
        "T_LOGICAL_XOR",
        "T_LOGICAL_AND",
        "T_PRINT",
        "'='",
        "T_PLUS_EQUAL",
        "T_MINUS_EQUAL",
        "T_MUL_EQUAL",
        "T_DIV_EQUAL",
        "T_CONCAT_EQUAL",
        "T_MOD_EQUAL",
        "T_AND_EQUAL",
        "T_OR_EQUAL",
        "T_XOR_EQUAL",
        "T_SL_EQUAL",
        "T_SR_EQUAL",
        "'?'",
        "':'",
        "T_BOOLEAN_OR",
        "T_BOOLEAN_AND",
        "'|'",
        "'^'",
        "'&'",
        "T_IS_EQUAL",
        "T_IS_NOT_EQUAL",
        "T_IS_IDENTICAL",
        "T_IS_NOT_IDENTICAL",
        "'<'",
        "T_IS_SMALLER_OR_EQUAL",
        "'>'",
        "T_IS_GREATER_OR_EQUAL",
        "T_SL",
        "T_SR",
        "'+'",
        "'-'",
        "'.'",
        "'*'",
        "'/'",
        "'%'",
        "'!'",
        "T_INSTANCEOF",
        "'~'",
        "T_INC",
        "T_DEC",
        "T_INT_CAST",
        "T_DOUBLE_CAST",
        "T_STRING_CAST",
        "T_ARRAY_CAST",
        "T_OBJECT_CAST",
        "T_BOOL_CAST",
        "T_UNSET_CAST",
        "'@'",
        "'['",
        "T_NEW",
        "T_CLONE",
        "T_EXIT",
        "T_IF",
        "T_ELSEIF",
        "T_ELSE",
        "T_ENDIF",
        "T_LNUMBER",
        "T_DNUMBER",
        "T_STRING",
        "T_STRING_VARNAME",
        "T_VARIABLE",
        "T_NUM_STRING",
        "T_INLINE_HTML",
        "T_ENCAPSED_AND_WHITESPACE",
        "T_CONSTANT_ENCAPSED_STRING",
        "T_ECHO",
        "T_DO",
        "T_WHILE",
        "T_ENDWHILE",
        "T_FOR",
        "T_ENDFOR",
        "T_FOREACH",
        "T_ENDFOREACH",
        "T_DECLARE",
        "T_ENDDECLARE",
        "T_AS",
        "T_SWITCH",
        "T_ENDSWITCH",
        "T_CASE",
        "T_DEFAULT",
        "T_BREAK",
        "T_CONTINUE",
        "T_GOTO",
        "T_FUNCTION",
        "T_CONST",
        "T_RETURN",
        "T_TRY",
        "T_CATCH",
        "T_THROW",
        "T_USE",
        "T_GLOBAL",
        "T_STATIC",
        "T_ABSTRACT",
        "T_FINAL",
        "T_PRIVATE",
        "T_PROTECTED",
        "T_PUBLIC",
        "T_VAR",
        "T_UNSET",
        "T_ISSET",
        "T_EMPTY",
        "T_HALT_COMPILER",
        "T_CLASS",
        "T_INTERFACE",
        "T_EXTENDS",
        "T_IMPLEMENTS",
        "T_OBJECT_OPERATOR",
        "T_DOUBLE_ARROW",
        "T_LIST",
        "T_ARRAY",
        "T_CLASS_C",
        "T_METHOD_C",
        "T_FUNC_C",
        "T_LINE",
        "T_FILE",
        "T_START_HEREDOC",
        "T_END_HEREDOC",
        "T_DOLLAR_OPEN_CURLY_BRACES",
        "T_CURLY_OPEN",
        "T_PAAMAYIM_NEKUDOTAYIM",
        "T_NAMESPACE",
        "T_NS_C",
        "T_DIR",
        "T_NS_SEPARATOR",
        "';'",
        "'{'",
        "'}'",
        "'('",
        "')'",
        "'$'",
        "'`'",
        "']'",
        "'\"'"
        , "???"
    );

    private static $yyproduction = array(
        '$start : start',
        "start : top_statement_list",
        "top_statement_list : top_statement_list top_statement",
        "top_statement_list : /* empty */",
        "namespace_name : namespace_name_sub",
        "namespace_name_sub : T_STRING",
        "namespace_name_sub : namespace_name_sub T_NS_SEPARATOR T_STRING",
        "top_statement : statement",
        "top_statement : function_declaration_statement",
        "top_statement : class_declaration_statement",
        "top_statement : T_HALT_COMPILER",
        "top_statement : T_NAMESPACE namespace_name ';'",
        "top_statement : T_NAMESPACE namespace_name '{' top_statement_list '}'",
        "top_statement : T_NAMESPACE '{' top_statement_list '}'",
        "top_statement : T_USE use_declarations ';'",
        "top_statement : constant_declaration ';'",
        "use_declarations : use_declarations ',' use_declaration",
        "use_declarations : use_declaration",
        "use_declaration : namespace_name",
        "use_declaration : namespace_name T_AS T_STRING",
        "use_declaration : T_NS_SEPARATOR namespace_name",
        "use_declaration : T_NS_SEPARATOR namespace_name T_AS T_STRING",
        "constant_declaration : constant_declaration ',' T_STRING '=' static_scalar",
        "constant_declaration : T_CONST T_STRING '=' static_scalar",
        "inner_statement_list : inner_statement_list inner_statement",
        "inner_statement_list : /* empty */",
        "inner_statement : statement",
        "inner_statement : function_declaration_statement",
        "inner_statement : class_declaration_statement",
        "inner_statement : T_HALT_COMPILER",
        "statement : '{' inner_statement_list '}'",
        "statement : T_IF '(' expr ')' statement elseif_list else_single",
        "statement : T_IF '(' expr ')' ':' inner_statement_list new_elseif_list new_else_single T_ENDIF ';'",
        "statement : T_WHILE '(' expr ')' while_statement",
        "statement : T_DO statement T_WHILE '(' expr ')' ';'",
        "statement : T_FOR '(' for_expr ';' for_expr ';' for_expr ')' for_statement",
        "statement : T_SWITCH '(' expr ')' switch_case_list",
        "statement : T_BREAK ';'",
        "statement : T_BREAK expr ';'",
        "statement : T_CONTINUE ';'",
        "statement : T_CONTINUE expr ';'",
        "statement : T_RETURN ';'",
        "statement : T_RETURN expr ';'",
        "statement : T_GLOBAL global_var_list ';'",
        "statement : T_STATIC static_var_list ';'",
        "statement : T_ECHO expr_list ';'",
        "statement : T_INLINE_HTML",
        "statement : expr ';'",
        "statement : T_UNSET '(' variables_list ')' ';'",
        "statement : T_FOREACH '(' expr T_AS variable ')' foreach_statement",
        "statement : T_FOREACH '(' expr T_AS '&' variable ')' foreach_statement",
        "statement : T_FOREACH '(' expr T_AS variable T_DOUBLE_ARROW optional_ref variable ')' foreach_statement",
        "statement : T_DECLARE '(' declare_list ')' declare_statement",
        "statement : ';'",
        "statement : T_TRY '{' inner_statement_list '}' catches",
        "statement : T_THROW expr ';'",
        "statement : T_GOTO T_STRING ';'",
        "statement : T_STRING ':'",
        "catches : catch",
        "catches : catches catch",
        "catch : T_CATCH '(' name T_VARIABLE ')' '{' inner_statement_list '}'",
        "variables_list : variable",
        "variables_list : variables_list ',' variable",
        "optional_ref : /* empty */",
        "optional_ref : '&'",
        "function_declaration_statement : T_FUNCTION optional_ref T_STRING '(' parameter_list ')' '{' inner_statement_list '}'",
        "class_declaration_statement : class_entry_type T_STRING extends_from implements_list '{' class_statement_list '}'",
        "class_declaration_statement : T_INTERFACE T_STRING interface_extends_list '{' class_statement_list '}'",
        "class_entry_type : T_CLASS",
        "class_entry_type : T_ABSTRACT T_CLASS",
        "class_entry_type : T_FINAL T_CLASS",
        "extends_from : /* empty */",
        "extends_from : T_EXTENDS name",
        "interface_extends_list : /* empty */",
        "interface_extends_list : T_EXTENDS interface_list",
        "implements_list : /* empty */",
        "implements_list : T_IMPLEMENTS interface_list",
        "interface_list : name",
        "interface_list : interface_list ',' name",
        "for_statement : statement",
        "for_statement : ':' inner_statement_list T_ENDFOR ';'",
        "foreach_statement : statement",
        "foreach_statement : ':' inner_statement_list T_ENDFOREACH ';'",
        "declare_statement : statement",
        "declare_statement : ':' inner_statement_list T_ENDDECLARE ';'",
        "declare_list : T_STRING '=' static_scalar",
        "declare_list : declare_list ',' T_STRING '=' static_scalar",
        "switch_case_list : '{' case_list '}'",
        "switch_case_list : '{' ';' case_list '}'",
        "switch_case_list : ':' case_list T_ENDSWITCH ';'",
        "switch_case_list : ':' ';' case_list T_ENDSWITCH ';'",
        "case_list : /* empty */",
        "case_list : case_list T_CASE expr case_separator inner_statement_list",
        "case_list : case_list T_DEFAULT case_separator inner_statement_list",
        "case_separator : ':'",
        "case_separator : ';'",
        "while_statement : statement",
        "while_statement : ':' inner_statement_list T_ENDWHILE ';'",
        "elseif_list : /* empty */",
        "elseif_list : elseif_list T_ELSEIF '(' expr ')' statement",
        "new_elseif_list : /* empty */",
        "new_elseif_list : new_elseif_list T_ELSEIF '(' expr ')' ':' inner_statement_list",
        "else_single : /* empty */",
        "else_single : T_ELSE statement",
        "new_else_single : /* empty */",
        "new_else_single : T_ELSE ':' inner_statement_list",
        "parameter_list : non_empty_parameter_list",
        "parameter_list : /* empty */",
        "non_empty_parameter_list : optional_class_type optional_ref T_VARIABLE",
        "non_empty_parameter_list : optional_class_type optional_ref T_VARIABLE '=' static_scalar",
        "non_empty_parameter_list : non_empty_parameter_list ',' optional_class_type optional_ref T_VARIABLE",
        "non_empty_parameter_list : non_empty_parameter_list ',' optional_class_type optional_ref T_VARIABLE '=' static_scalar",
        "optional_class_type : /* empty */",
        "optional_class_type : name",
        "optional_class_type : T_ARRAY",
        "function_call_argument_list : non_empty_function_call_argument_list",
        "function_call_argument_list : /* empty */",
        "non_empty_function_call_argument_list : expr",
        "non_empty_function_call_argument_list : '&' variable",
        "non_empty_function_call_argument_list : non_empty_function_call_argument_list ',' expr",
        "non_empty_function_call_argument_list : non_empty_function_call_argument_list ',' '&' variable",
        "global_var_list : global_var_list ',' global_var",
        "global_var_list : global_var",
        "global_var : T_VARIABLE",
        "global_var : '$' variable",
        "global_var : '$' '{' expr '}'",
        "static_var_list : static_var_list ',' T_VARIABLE",
        "static_var_list : static_var_list ',' T_VARIABLE '=' static_scalar",
        "static_var_list : T_VARIABLE",
        "static_var_list : T_VARIABLE '=' static_scalar",
        "class_statement_list : class_statement_list class_statement",
        "class_statement_list : /* empty */",
        "class_statement : variable_modifiers class_variable_declaration ';'",
        "class_statement : class_constant_declaration ';'",
        "class_statement : method_modifiers T_FUNCTION optional_ref T_STRING '(' parameter_list ')' method_body",
        "method_body : ';'",
        "method_body : '{' inner_statement_list '}'",
        "variable_modifiers : non_empty_member_modifiers",
        "variable_modifiers : T_VAR",
        "method_modifiers : /* empty */",
        "method_modifiers : non_empty_member_modifiers",
        "non_empty_member_modifiers : member_modifier",
        "non_empty_member_modifiers : non_empty_member_modifiers member_modifier",
        "member_modifier : T_PUBLIC",
        "member_modifier : T_PROTECTED",
        "member_modifier : T_PRIVATE",
        "member_modifier : T_STATIC",
        "member_modifier : T_ABSTRACT",
        "member_modifier : T_FINAL",
        "class_variable_declaration : class_variable_declaration ',' T_VARIABLE",
        "class_variable_declaration : class_variable_declaration ',' T_VARIABLE '=' static_scalar",
        "class_variable_declaration : T_VARIABLE",
        "class_variable_declaration : T_VARIABLE '=' static_scalar",
        "class_constant_declaration : class_constant_declaration ',' T_STRING '=' static_scalar",
        "class_constant_declaration : T_CONST T_STRING '=' static_scalar",
        "expr_list : expr_list ',' expr",
        "expr_list : expr",
        "for_expr : /* empty */",
        "for_expr : expr_list",
        "expr : variable",
        "expr : T_LIST '(' assignment_list ')' '=' expr",
        "expr : variable '=' expr",
        "expr : variable '=' '&' variable",
        "expr : variable '=' '&' T_NEW class_name_reference ctor_arguments",
        "expr : T_NEW class_name_reference ctor_arguments",
        "expr : T_CLONE expr",
        "expr : variable T_PLUS_EQUAL expr",
        "expr : variable T_MINUS_EQUAL expr",
        "expr : variable T_MUL_EQUAL expr",
        "expr : variable T_DIV_EQUAL expr",
        "expr : variable T_CONCAT_EQUAL expr",
        "expr : variable T_MOD_EQUAL expr",
        "expr : variable T_AND_EQUAL expr",
        "expr : variable T_OR_EQUAL expr",
        "expr : variable T_XOR_EQUAL expr",
        "expr : variable T_SL_EQUAL expr",
        "expr : variable T_SR_EQUAL expr",
        "expr : variable T_INC",
        "expr : T_INC variable",
        "expr : variable T_DEC",
        "expr : T_DEC variable",
        "expr : expr T_BOOLEAN_OR expr",
        "expr : expr T_BOOLEAN_AND expr",
        "expr : expr T_LOGICAL_OR expr",
        "expr : expr T_LOGICAL_AND expr",
        "expr : expr T_LOGICAL_XOR expr",
        "expr : expr '|' expr",
        "expr : expr '&' expr",
        "expr : expr '^' expr",
        "expr : expr '.' expr",
        "expr : expr '+' expr",
        "expr : expr '-' expr",
        "expr : expr '*' expr",
        "expr : expr '/' expr",
        "expr : expr '%' expr",
        "expr : expr T_SL expr",
        "expr : expr T_SR expr",
        "expr : '+' expr",
        "expr : '-' expr",
        "expr : '!' expr",
        "expr : '~' expr",
        "expr : expr T_IS_IDENTICAL expr",
        "expr : expr T_IS_NOT_IDENTICAL expr",
        "expr : expr T_IS_EQUAL expr",
        "expr : expr T_IS_NOT_EQUAL expr",
        "expr : expr '<' expr",
        "expr : expr T_IS_SMALLER_OR_EQUAL expr",
        "expr : expr '>' expr",
        "expr : expr T_IS_GREATER_OR_EQUAL expr",
        "expr : expr T_INSTANCEOF class_name_reference",
        "expr : '(' expr ')'",
        "expr : expr '?' expr ':' expr",
        "expr : expr '?' ':' expr",
        "expr : T_ISSET '(' variables_list ')'",
        "expr : T_EMPTY '(' variable ')'",
        "expr : T_INCLUDE expr",
        "expr : T_INCLUDE_ONCE expr",
        "expr : T_EVAL '(' expr ')'",
        "expr : T_REQUIRE expr",
        "expr : T_REQUIRE_ONCE expr",
        "expr : T_INT_CAST expr",
        "expr : T_DOUBLE_CAST expr",
        "expr : T_STRING_CAST expr",
        "expr : T_ARRAY_CAST expr",
        "expr : T_OBJECT_CAST expr",
        "expr : T_BOOL_CAST expr",
        "expr : T_UNSET_CAST expr",
        "expr : T_EXIT exit_expr",
        "expr : '@' expr",
        "expr : scalar",
        "expr : T_ARRAY '(' array_pair_list ')'",
        "expr : '`' backticks_expr '`'",
        "expr : T_PRINT expr",
        "expr : T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars '{' inner_statement_list '}'",
        "lexical_vars : /* empty */",
        "lexical_vars : T_USE '(' lexical_var_list ')'",
        "lexical_var_list : lexical_var_list ',' optional_ref T_VARIABLE",
        "lexical_var_list : optional_ref T_VARIABLE",
        "function_call : name '(' function_call_argument_list ')'",
        "function_call : class_name T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_argument_list ')'",
        "function_call : reference_variable T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_argument_list ')'",
        "function_call : static_property_with_arrays '(' function_call_argument_list ')'",
        "function_call : variable_without_objects '(' function_call_argument_list ')'",
        "class_name : T_STATIC",
        "class_name : name",
        "name : namespace_name",
        "name : T_NAMESPACE T_NS_SEPARATOR namespace_name",
        "name : T_NS_SEPARATOR namespace_name",
        "class_name_reference : class_name",
        "class_name_reference : dynamic_class_name_reference",
        "dynamic_class_name_reference : object_access_for_dcnr",
        "dynamic_class_name_reference : base_variable",
        "object_access_for_dcnr : /* empty */",
        "object_access_for_dcnr : base_variable T_OBJECT_OPERATOR object_property",
        "object_access_for_dcnr : object_access_for_dcnr T_OBJECT_OPERATOR object_property",
        "object_access_for_dcnr : object_access_for_dcnr '[' dim_offset ']'",
        "object_access_for_dcnr : object_access_for_dcnr '{' expr '}'",
        "exit_expr : /* empty */",
        "exit_expr : '(' ')'",
        "exit_expr : '(' expr ')'",
        "backticks_expr : /* empty */",
        "backticks_expr : T_ENCAPSED_AND_WHITESPACE",
        "backticks_expr : encaps_list",
        "ctor_arguments : /* empty */",
        "ctor_arguments : '(' function_call_argument_list ')'",
        "common_scalar : T_LNUMBER",
        "common_scalar : T_DNUMBER",
        "common_scalar : T_CONSTANT_ENCAPSED_STRING",
        "common_scalar : T_LINE",
        "common_scalar : T_FILE",
        "common_scalar : T_DIR",
        "common_scalar : T_CLASS_C",
        "common_scalar : T_METHOD_C",
        "common_scalar : T_FUNC_C",
        "common_scalar : T_NS_C",
        "common_scalar : T_START_HEREDOC T_ENCAPSED_AND_WHITESPACE T_END_HEREDOC",
        "common_scalar : T_START_HEREDOC T_END_HEREDOC",
        "static_scalar : common_scalar",
        "static_scalar : name",
        "static_scalar : '+' static_scalar",
        "static_scalar : '-' static_scalar",
        "static_scalar : T_ARRAY '(' static_array_pair_list ')'",
        "static_scalar : class_name T_PAAMAYIM_NEKUDOTAYIM T_STRING",
        "scalar : T_STRING_VARNAME",
        "scalar : class_constant",
        "scalar : name",
        "scalar : common_scalar",
        "scalar : '\"' encaps_list '\"'",
        "scalar : T_START_HEREDOC encaps_list T_END_HEREDOC",
        "static_array_pair_list : /* empty */",
        "static_array_pair_list : non_empty_static_array_pair_list optional_comma",
        "optional_comma : /* empty */",
        "optional_comma : ','",
        "non_empty_static_array_pair_list : non_empty_static_array_pair_list ',' static_scalar T_DOUBLE_ARROW static_scalar",
        "non_empty_static_array_pair_list : non_empty_static_array_pair_list ',' static_scalar",
        "non_empty_static_array_pair_list : static_scalar T_DOUBLE_ARROW static_scalar",
        "non_empty_static_array_pair_list : static_scalar",
        "variable : object_access",
        "variable : base_variable",
        "variable : function_call",
        "object_access : object_access_arrayable",
        "object_access : object_access_arrayable '(' function_call_argument_list ')'",
        "object_access : variable T_OBJECT_OPERATOR object_property '(' function_call_argument_list ')'",
        "object_access_arrayable : variable T_OBJECT_OPERATOR object_property",
        "object_access_arrayable : object_access_arrayable '[' dim_offset ']'",
        "object_access_arrayable : object_access_arrayable '{' expr '}'",
        "variable_without_objects : reference_variable",
        "variable_without_objects : '$' reference_variable",
        "base_variable : variable_without_objects",
        "base_variable : class_name T_PAAMAYIM_NEKUDOTAYIM '$' reference_variable",
        "base_variable : reference_variable T_PAAMAYIM_NEKUDOTAYIM '$' reference_variable",
        "base_variable : static_property_with_arrays",
        "static_property_with_arrays : class_name T_PAAMAYIM_NEKUDOTAYIM T_VARIABLE",
        "static_property_with_arrays : reference_variable T_PAAMAYIM_NEKUDOTAYIM T_VARIABLE",
        "static_property_with_arrays : class_name T_PAAMAYIM_NEKUDOTAYIM '$' '{' expr '}'",
        "static_property_with_arrays : reference_variable T_PAAMAYIM_NEKUDOTAYIM '$' '{' expr '}'",
        "static_property_with_arrays : static_property_with_arrays '[' dim_offset ']'",
        "static_property_with_arrays : static_property_with_arrays '{' expr '}'",
        "reference_variable : reference_variable '[' dim_offset ']'",
        "reference_variable : reference_variable '{' expr '}'",
        "reference_variable : T_VARIABLE",
        "reference_variable : '$' '{' expr '}'",
        "dim_offset : /* empty */",
        "dim_offset : expr",
        "object_property : T_STRING",
        "object_property : '{' expr '}'",
        "object_property : variable_without_objects",
        "assignment_list : assignment_list ',' assignment_list_element",
        "assignment_list : assignment_list_element",
        "assignment_list_element : variable",
        "assignment_list_element : T_LIST '(' assignment_list ')'",
        "assignment_list_element : /* empty */",
        "array_pair_list : /* empty */",
        "array_pair_list : non_empty_array_pair_list optional_comma",
        "non_empty_array_pair_list : non_empty_array_pair_list ',' expr T_DOUBLE_ARROW expr",
        "non_empty_array_pair_list : non_empty_array_pair_list ',' expr",
        "non_empty_array_pair_list : expr T_DOUBLE_ARROW expr",
        "non_empty_array_pair_list : expr",
        "non_empty_array_pair_list : non_empty_array_pair_list ',' expr T_DOUBLE_ARROW '&' variable",
        "non_empty_array_pair_list : non_empty_array_pair_list ',' '&' variable",
        "non_empty_array_pair_list : expr T_DOUBLE_ARROW '&' variable",
        "non_empty_array_pair_list : '&' variable",
        "encaps_list : encaps_list encaps_var",
        "encaps_list : encaps_list T_ENCAPSED_AND_WHITESPACE",
        "encaps_list : encaps_var",
        "encaps_list : T_ENCAPSED_AND_WHITESPACE encaps_var",
        "encaps_var : T_VARIABLE",
        "encaps_var : T_VARIABLE '[' encaps_var_offset ']'",
        "encaps_var : T_VARIABLE T_OBJECT_OPERATOR T_STRING",
        "encaps_var : T_DOLLAR_OPEN_CURLY_BRACES expr '}'",
        "encaps_var : T_DOLLAR_OPEN_CURLY_BRACES T_STRING_VARNAME '[' expr ']' '}'",
        "encaps_var : T_CURLY_OPEN variable '}'",
        "encaps_var_offset : T_STRING",
        "encaps_var_offset : T_NUM_STRING",
        "encaps_var_offset : T_VARIABLE",
        "class_constant : class_name T_PAAMAYIM_NEKUDOTAYIM T_STRING",
        "class_constant : reference_variable T_PAAMAYIM_NEKUDOTAYIM T_STRING"
    );

    private static $yytranslate = array(
            0,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,   47,  144,  145,  141,   46,   30,  145,
          139,  140,   44,   41,    7,   42,   43,   45,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,   25,  136,
           35,   12,   37,   24,   59,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,   60,  145,  143,   29,  145,  142,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  137,   28,  138,   49,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,    1,    2,    3,    4,
            5,    6,    8,    9,   10,   11,   13,   14,   15,   16,
           17,   18,   19,   20,   21,   22,   23,   26,   27,   31,
           32,   33,   34,   36,   38,   39,   40,   48,   50,   51,
           52,   53,   54,   55,   56,   57,   58,   61,   62,   63,
           64,   65,   66,   67,   68,   69,   70,   71,   72,   73,
           74,  145,  145,   75,   76,   77,   78,   79,   80,   81,
           82,   83,   84,   85,   86,   87,   88,   89,   90,   91,
           92,   93,   94,   95,   96,   97,   98,   99,  100,  101,
          102,  103,  104,  105,  106,  107,  108,  109,  110,  111,
          112,  113,  114,  115,  116,  117,  118,  119,  120,  121,
          122,  123,  124,  125,  126,  145,  145,  145,  145,  145,
          145,  127,  128,  129,  130,  131,  132,  133,  134,  135
    );

    private static $yyaction = array(
           56,   57,  366,   58,   59,-32766,-32766,-32766,  255,   60,
        -32767,-32767,-32767,-32767,   99,  100,  101,  102,  103,  856,
          849,-32766,    0,-32766,-32766,   43,  107,  108,  109,  110,
          111,  112,  113,  114,  115,  116,  117,-32766,-32766,   61,
           62,-32766,-32766,-32766,-32766,   63,  521,   64,  240,  241,
           65,   66,   67,   68,   69,   70,   71,   72,-32766,  253,
           73,  343,  367,  713,  715,  246,  801,  802,  368,  819,
          856,  501,  582,  364,  803,   51,   26,  369,  122,  370,
          630,  371,  484,  372,-32766,  486,  373,  209,  279,  284,
           38,   39,  374,  346,  344,   40,  376,  344,   74,  247,
          302,  345,  650,  377,  378,  492,  377,  378,  379,  380,
          381,  856,  565,  604,  382,  565,  604,  382,  383,  384,
          807,  808,  809,  804,  805,  259,  200,   83,   84,   85,
          389,  810,  806,  338,  589,  514,  124,   75,   54,  279,
          263,  856,  267,   42,  317,   86,   87,   88,   89,   90,
           91,   92,   93,   94,   95,   96,   97,   98,   99,  100,
          101,  102,  103,  104,  105,  106,   55,  254,  682,  683,
          684,  681,  680,  679,  541,-32766,  123,-32766,-32766,-32766,
          279,  541,  252,  206,  856,-32766,  262,  442,-32766,-32766,
        -32766,  631,-32766,-32766,-32766,-32766,-32766,  354,  860,-32766,
          668,   81,  243,-32766,-32766,-32766,  771,  779,-32766,-32766,
          279,-32766,  301,-32766,  779,  120,-32766,   33,-32766,-32766,
        -32766,-32766,  541,  250,  265,  888,-32766,  890,  889,-32766,
        -32766,-32766,  418,-32766,  669,-32766,  389,-32766,  477,  338,
        -32766,   53,  282,  121,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,  674,-32766,  279,-32766,  779,   52,-32766,  276,  405,
          477,   45,-32766,  541,  233,  125,  856,-32766,   46,  279,
        -32766,-32766,-32766,  674,-32766,  231,-32766,  415,-32766,-32766,
          603,-32766,  101,  102,  103,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,  856,-32766,  303,-32766,  779,  650,-32766,  522,
          421,  207,  602,-32766,  541,  244,  462,  848,-32766,  355,
          659,-32766,-32766,-32766,  879,-32766, -107,-32766,  119,-32766,
           20,  268,-32766,  340,  215,  516,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,  118,-32766,  279,-32766,  779,  879,-32766,
          205,  550,  214,  359,-32766,  541,  245,  493,  494,-32766,
          483,   27,-32766,-32766,-32766,  212,-32766,  131,-32766,  812,
        -32766,  402,  204,-32766,   21,  671,  535,-32766,-32766,-32766,
        -32766,-32766,-32766,-32766,  812,-32766,  283,-32766,  779,  232,
        -32766,  132,  307,  823,  201,-32766,  541,  249,  551,  210,
        -32766,  824,  211,-32766,-32766,-32766,  199,-32766,  593,-32766,
          534,-32766,  547,  517,-32766,  505,  580,  128,-32766,-32766,
        -32766,-32766,-32766,-32766,-32766,  526,-32766,  531,-32766,  779,
          519,-32766,  530,  579,  600,  127,-32766,  541,  248,  254,
          541,-32766,  509,  542,-32766,-32766,-32766,  884,-32766,  555,
        -32766,  818,-32766,  879,  502,-32766,  557,  496,  490,-32766,
        -32766,-32766,-32766,-32766,-32766,-32766,  459,-32766,  349,-32766,
          779,  350,-32766,  398,  281,  363,  399,-32766,  541,-32766,
        -32766,-32766,-32766,  410,  411,-32766,-32766,-32766,  423,-32766,
          430,-32766,  432,-32766,  438,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,-32766,-32766,-32766,-32766,  439,-32766,  280,
        -32766,  779,  772,-32766,  773, -137,  401,  508,-32766,  541,
          104,  105,  106,-32766,  254,  500,-32766,-32766,-32766,  498,
        -32766,  491,-32766,  474,-32766,  449,  487,-32766,  413,  407,
          464,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  273,-32766,
          606,-32766,  779,  448,-32766,  234,  472,-32766,   47,-32766,
          541,  422,  605,  274,-32766,  339,  275,-32766,-32766,-32766,
          266,-32766,   82,-32766,  264,-32766,  624,  213,-32766,    0,
            0,   44,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  794,
        -32766,  208,-32766,  779,    0,-32766,  291,  811,  292,  404,
        -32766,  541,    0,    0, -244,-32766,  293,  130,-32766,-32766,
        -32766,  320,-32766,  463,-32766,  294,-32766,  321,  340,-32766,
           78,   49,  883,-32766,-32766,-32766,-32766,-32766,-32766,-32766,
          583,-32766,  574,-32766,  779,  576,-32766,  578,  591,  581,
           50,-32766,  541,  616,  618,  568,-32766,  626,  625,-32766,
        -32766,-32766,  620,-32766,  633,-32766,  570,-32766,  584,  592,
        -32766,  525,  524,   77,-32766,-32766,-32766,  577,-32766,-32766,
        -32766,  533,-32766,  515,-32766,  779,  518,-32766,  523,  527,
          528,  129,-32766,  541,  623,  887,  885,-32766,  857,  855,
        -32766,-32766,-32766,  853,-32766,  841,-32766,  792,-32766,  661,
          861,-32766,  850,  851,  886,-32766,-32766,-32766,  573,-32766,
        -32766,-32766,   76,-32766,   48,-32766,  779,   31,-32766,   41,
           37,   36,   35,-32766,  541,   34,   32,  257,-32766,   30,
           79,-32766,-32766,-32766,   80,-32766,  126,-32766,  133,-32766,
          202,  203,-32766,  134,  348,  342,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,  344,-32766,  277,-32766,  779,-32766,-32766,
        -32766,  258,  377,  378,-32766,  198,  256,  801,  802,  242,
          239,  565,  604,  382,-32766,  803,-32766,-32766,-32766,-32766,
        -32766,-32767,-32767,-32767,-32767,  238,  230,  229,   29,    0,
          575,-32766,  344,  375,  767,  749,  341,  450,  300,  344,
          375,  377,  378,  866,  452,  300,  750,  795,  377,  378,
          546,  604,  382,  753,  746,  511,  470,  546,  604,  382,
          445,  807,  808,  809,  804,  805,  329,  304,   28,  278,
           25,   19,  810,  806,   23,  549,  278,  344,  375,  791,
           24,   22,  548,  300,    0,  817,  377,  378,-32766,-32766,
        -32766,  776,  775,  344,  838,  546,  604,  382,  800,-32766,
        -32766,-32766,  377,  378,-32766,  837,-32766,-32766,-32766,-32766,
        -32766,  565,  604,  382,  278,-32766,  778,-32766,-32766,-32766,
        -32766,  344,  840,  777,  344,  774,  766,  507,  344,  471,
          377,  378,  356,  377,  378,  308,  601,  377,  378,  565,
          604,  382,  565,  604,  382,  852,  565,  604,  382,  854,
          344,  481,  512,    0,    0,    0,    0,    0,    0,  377,
          378,  506,    0,    0,  672,  344,    0,  596,  565,  604,
          382,  769,  344,    0,  377,  378,    0,  344,    0,    0,
            0,  377,  378,  565,  604,  382,  377,  378,    0,    0,
          565,  604,  382,  566,    0,  565,  604,  382
    );

    private static $yycheck = array(
            2,    3,    4,    5,    6,    8,    9,   10,   61,   11,
           35,   36,   37,   38,   39,   40,   41,   42,   43,   72,
           72,   24,    0,   26,   27,   12,   13,   14,   15,   16,
           17,   18,   19,   20,   21,   22,   23,    8,    9,   41,
           42,    8,    8,    9,   10,   47,   25,   49,   50,   51,
           52,   53,   54,   55,   56,   57,   58,   59,   24,   61,
           62,   63,   64,   50,   51,    7,   68,   69,   70,   71,
           72,    7,   74,    7,   76,   77,   78,   79,    7,   81,
           25,   83,   80,   85,  103,   86,   88,    7,  141,  141,
           92,   93,   94,   95,   95,   97,   98,   95,  100,   30,
          102,  103,  121,  104,  105,    7,  104,  105,  110,  111,
          112,   72,  113,  114,  115,  113,  114,  115,  120,  121,
          122,  123,  124,  125,  126,  127,   12,    8,    9,   10,
          132,  133,  134,  135,  136,  137,   25,  139,   60,  141,
          142,   72,  144,   24,   72,   26,   27,   28,   29,   30,
           31,   32,   33,   34,   35,   36,   37,   38,   39,   40,
           41,   42,   43,   44,   45,   46,   60,   48,  103,  104,
          105,  106,  107,  108,   70,   64,  137,    8,    9,   10,
          141,   70,   30,   12,   72,   74,    7,    7,   77,   78,
           79,  136,   81,   24,   83,   26,   85,   25,   70,   88,
          136,  129,  130,   92,   93,   94,  140,  103,   97,   98,
          141,  100,    7,  102,  103,  137,   64,  139,    8,    9,
           10,  110,   70,   30,  118,   70,   74,   72,   73,   77,
           78,   79,  120,   81,  136,   83,  132,   85,   96,  135,
           88,   60,   60,  137,   92,   93,   94,  136,  137,   97,
           98,  109,  100,  141,  102,  103,   60,   64,    7,    7,
           96,    7,  110,   70,   30,  137,   72,   74,    7,  141,
           77,   78,   79,  109,   81,    7,   83,    7,   85,  103,
          138,   88,   41,   42,   43,   92,   93,   94,  136,  137,
           97,   98,   72,  100,    7,  102,  103,  121,   64,   25,
          118,   12,  138,  110,   70,   30,   75,   72,   74,  137,
           72,   77,   78,   79,   75,   81,  140,   83,  137,   85,
          140,   75,   88,  135,   12,  137,   92,   93,   94,  136,
          137,   97,   98,  137,  100,  141,  102,  103,   75,   64,
           12,  136,   12,  119,  110,   70,   30,   65,   66,   74,
           65,   66,   77,   78,   79,   12,   81,   12,   83,  128,
           85,  141,   12,   88,  140,  136,  137,   92,   93,   94,
          136,  137,   97,   98,  128,  100,  141,  102,  103,  141,
           64,   90,   91,  144,   12,  110,   70,   30,  136,   12,
           74,  128,   12,   77,   78,   79,   12,   81,   25,   83,
           25,   85,  136,  137,   88,   67,  136,   25,   92,   93,
           94,  136,  137,   97,   98,   25,  100,   25,  102,  103,
           25,   64,   25,  136,   30,   60,  110,   70,   30,   48,
           70,   74,   70,   70,   77,   78,   79,   70,   81,   70,
           83,   70,   85,   75,   70,   88,   70,   89,   70,   92,
           93,   94,  136,  137,   97,   98,   70,  100,   70,  102,
          103,   70,   64,   70,   75,   95,   70,  110,   70,    8,
            9,   10,   74,   70,   70,   77,   78,   79,   70,   81,
           70,   83,   70,   85,   70,   24,   88,   26,   27,   28,
           92,   93,   94,  136,  137,   97,   98,   70,  100,   75,
          102,  103,   72,   64,   72,   72,   72,   72,  110,   70,
           44,   45,   46,   74,   48,   72,   77,   78,   79,   72,
           81,   72,   83,   72,   85,   72,   89,   88,   87,   79,
           99,   92,   93,   94,  136,  137,   97,   98,  116,  100,
          114,  102,  103,   87,   64,   87,  101,  103,  119,  110,
           70,   71,  114,  117,   74,  135,  116,   77,   78,   79,
          118,   81,  137,   83,  118,   85,  138,  119,   88,   -1,
           -1,  119,   92,   93,   94,  136,  137,   97,   98,  140,
          100,  119,  102,  103,   -1,   64,  131,  128,  131,  135,
          110,   70,   -1,   -1,  131,   74,  131,  137,   77,   78,
           79,  131,   81,  131,   83,  131,   85,  131,  135,   88,
          139,  136,  143,   92,   93,   94,  136,  137,   97,   98,
          136,  100,  136,  102,  103,  136,   64,  136,  136,  136,
          136,  110,   70,  136,  136,  136,   74,  136,  136,   77,
           78,   79,  136,   81,  136,   83,  136,   85,  136,  136,
           88,  136,  136,  139,   92,   93,   94,  136,  137,   97,
           98,  137,  100,  137,  102,  103,  137,   64,  137,  137,
          137,  137,  110,   70,  138,  138,  138,   74,  138,  138,
           77,   78,   79,  138,   81,  138,   83,  138,   85,  138,
          138,   88,  138,  138,  138,   92,   93,   94,  136,  137,
           97,   98,  139,  100,  139,  102,  103,  139,   64,  139,
          139,  139,  139,  110,   70,  139,  139,  139,   74,  139,
          139,   77,   78,   79,  139,   81,  139,   83,  139,   85,
           41,   42,   88,  139,  139,  139,   92,   93,   94,  136,
          137,   97,   98,   95,  100,  139,  102,  103,    8,    9,
           10,  139,  104,  105,  110,  139,  139,   68,   69,  139,
          139,  113,  114,  115,   24,   76,   26,   27,   28,   29,
           30,   31,   32,   33,   34,  139,  139,  139,  139,   -1,
          136,  137,   95,   96,  142,  140,  138,  140,  101,   95,
           96,  104,  105,  140,  140,  101,  140,  140,  104,  105,
          113,  114,  115,  140,  140,  140,  140,  113,  114,  115,
          121,  122,  123,  124,  125,  126,  127,  140,  140,  132,
          140,  140,  133,  134,  140,  138,  132,   95,   96,  143,
          140,  140,  138,  101,   -1,  140,  104,  105,    8,    9,
           10,  140,  140,   95,  140,  113,  114,  115,  140,    8,
            9,   10,  104,  105,   24,  140,   26,   27,   28,   29,
           30,  113,  114,  115,  132,   24,  140,   26,   27,   28,
           29,   95,  143,  140,   95,  140,  140,  140,   95,  140,
          104,  105,  140,  104,  105,  140,  138,  104,  105,  113,
          114,  115,  113,  114,  115,  143,  113,  114,  115,  143,
           95,  143,   82,   -1,   -1,   -1,   -1,   -1,   -1,  104,
          105,   84,   -1,   -1,  138,   95,   -1,  138,  113,  114,
          115,  138,   95,   -1,  104,  105,   -1,   95,   -1,   -1,
           -1,  104,  105,  113,  114,  115,  104,  105,   -1,   -1,
          113,  114,  115,  138,   -1,  113,  114,  115
    );

    private static $yybase = array(
            0,  687,  694,  732,  805,  648,    2,   -1,  827,  748,
          783,  820,  779,  776,  832,  832,  832,  832,  832,   21,
          274,  390,  390,  392,  390,  395,   -2,   -2,   -2,  275,
          316,  316,  316,  316,  316,  316,  316,  316,  562,  644,
          521,  439,  111,  234,  398,  357,  193,  152,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  480,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,  603,  603,  603,  603,  603,
          603,  603,  603,  603,  603,   55,  484,  486,  489,  491,
          492,  664,  663,  657,  680,  681,  458,  677,  452,  538,
          540,  382,  541,  545,  547,  549,  551,  552,  666,  429,
          758,  554,  555,  678,  665,  119,  119,  119,  119,  119,
          119,  119,  119,  119,  119,  119,   33,   29,  210,  210,
          210,  210,  210,  210,  210,  210,  210,  210,  210,  210,
          210,  210,  210,   34,   34,  169,   -3,  461,  689,  689,
          689,  689,  689,  689,  689,  689,  689,  689,  689,  689,
          689,  689,  689,  689,  689,  689,  841,  830,  740,  740,
          740,  740,   13,  -25,  -25,  -25,  -25,  142,  164,  112,
          112,  112,   39,  -53,   69,  433,  241,  241,  194,  194,
          194,  194,  194,  194,  194,  194,  194,  194,  194,  194,
          194,  194,  194,  194,  194,  194,  176,  176,  176,  246,
          263,  239,  -19,  424,  128,  128,  128,  389,  459,  368,
          466,  466,  466,  444,  444,  444,  444,  444,  188,  425,
           72,   72,  155,  534,  460,  457,  568,   78,  474,  476,
          106,  410,  412,  414,  427,  224,  437,  536,  358,  428,
          420,  420,  238,  238,  172,  285,  282,   55,  229,  252,
          463,  181,  493,  404,  205,  287,  270,  182,  266,  196,
          235,  -52,  180,  647,  645,  656,  654,  537,  196,  231,
          196,  196,  653,  691,   98,   64,  690,   66,  360,  360,
          360,  431,  394,  570,  394,  434,  394,  576,  403,  422,
          440,  254,  436,  394,  516,  515,  445,  431,  576,  394,
          394,   80,  449,  394,  394,   22,  563,  514,  373,  471,
          565,  581,  595,  585,  393,  396,  526,  438,  426,  636,
          621,  620,  388,  638,  639,  454,  391,  455,  577,  473,
          381,  446,  580,  465,  446,  470,  442,  450,  513,  384,
          441,  114,  425,  642,  363,  408,  617,  587,  475,   71,
          372,  578,  456,  369,  446,  453,  446,  529,  637,  446,
          736,  367,  365,  350,  381,  381,  381,  573,  735,  261,
          572,  756,  571,  752,  733,  726,  729,  715,  572,  571,
          708,  686,  386,  742,  179,  616,  463,  472,  376,  328,
          512,  251,  345,  446,  469,  446,  446,  531,  494,  171,
          739,  451,  459,  371,  606,  446,  704,  251,  702,  701,
          510,  532,  596,  533,  289,  462,  695,  378,  370,  446,
          446,  556,  446,  589,  508,  684,  506,  502,  447,  443,
          377,  380,  374,  594,  397,  338,  501,  432,  343,  448,
          737,  435,  330,  362,  446,  499,  498,  524,  312,  612,
          430,  375,  497,  745,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,    0,    0,    0,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,  119,  119,  119,  119,  119,  119,  119,  119,  119,
          119,  119,  119,  119,  119,  119,  119,  119,  119,  119,
          119,  119,  119,  119,  119,  119,  119,  119,  119,  119,
          119,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,  119,  119,  119,  119,  119,  119,  119,  119,
          119,  119,  119,  119,  119,  119,  119,  119,  119,  119,
          119,  119,  119,  119,  104,  104,  104,  104,  104,  104,
          104,  104,  104,  104,  104,  104,  104,  104,  104,  104,
          104,  104,  119,  119,  119,  119,  119,  119,  446,  466,
          466,  466,  466,   65,   65,  104,  104,  104,  104,  104,
          104,   65,  466,  466,  104,  104,  104,  104,  104,  104,
          104,  104,  104,  104,  104,  104,  104,  104,  104,  104,
          104,  104,  104,  104,  104,   72,   72,   72,  104,   72,
          220,  220,  220,   72,   72,   72,    0,    0,    0,  104,
          104,  104,  104,  104,  360,  220,    0,    0,    0,  220,
          220,  196,  181,    0,  196,  196,    0,  235,  -52,  235,
          -52,  446,  291,  291,  291,  291,  360,  360,    0,    0,
            0,    0,    0,    0,    0,    0,  580,    0,   71,  617,
            0,    0,    0,    0,    0,    0,    0,    0,    0,   58,
           58,  446,  268,  446,    0,    0,    0,    0,  268,  446,
            0,    0,  446
    );

    private static $yydefault = array(
            3,32767,32767,    1,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  100,   93,  105,   92,  101,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,  332,
          116,  116,  116,  116,  116,  116,  116,  116,32767,32767,
        32767,32767,32767,32767,32767,  292,32767,32767,  157,  157,
          157,32767,  322,  322,  322,  322,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,  337,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,  335,
        32767,32767,32767,32767,32767,  215,  216,  218,  219,  156,
          117,  323,  155,  336,  119,  334,  183,  185,  232,  184,
          161,  166,  167,  168,  169,  170,  171,  172,  173,  174,
          175,  176,  160,  212,  211,  181,  182,  186,  289,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,  292,
        32767,32767,32767,32767,32767,32767,  188,  187,  203,  204,
          201,  202,  159,  205,  206,  207,  208,  139,  139,  331,
          331,  331,32767,32767,32767,  140,  195,  196,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,  252,  252,  252,  112,  112,  112,32767,
        32767,32767,  112,  260,32767,32767,32767,32767,32767,  262,
          190,  191,  189,32767,32767,32767,32767,32767,32767,32767,
          261,32767,32767,32767,32767,  306,  311,  300,  306,  306,
          250,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,  102,  104,32767,32767,32767,
          285,  311,32767,32767,32767,32767,32767,  346,32767,  307,
        32767,32767,32767,32767,32767,32767,32767,32767,  306,32767,
          309,  310,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,   63,  257,   63,  243,   63,  263,32767,   73,
           71,  291,   75,   63,   91,   91,  234,   54,  263,   63,
           63,  291,32767,   63,   63,32767,32767,32767,    5,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,    4,32767,32767,  308,32767,
          199,  178,  244,32767,  180,  248,  251,32767,32767,32767,
           18,  128,32767,32767,32767,32767,32767,32767,32767,  158,
        32767,32767,   20,32767,  124,32767,   61,32767,32767,  329,
        32767,32767,  283,32767,  192,  193,  194,  303,32767,  115,
          355,32767,  356,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  106,32767,  278,32767,32767,  126,
        32767,   74,32767,  341,32767,  162,  118,32767,32767,32767,
        32767,32767,32767,32767,32767,   62,32767,   76,32767,32767,
        32767,32767,32767,32767,  108,  296,32767,32767,32767,  340,
          339,32767,  120,32767,32767,32767,32767,32767,32767,32767,
        32767,  151,32767,32767,32767,32767,32767,32767,  110,  294,
        32767,32767,32767,32767,  338,32767,32767,32767,  149,32767,
        32767,32767,32767,32767,   25,   25,    3,    3,  131,   25,
           98,   25,   25,  131,   91,   91,   25,   25,   25,   25,
           25,   25,   25,   25,   25,   25
    );

    private static $yygoto = array(
          148,  170,  170,  170,  170,  170,  170,  170,  170,  137,
          138,  139,  143,  151,  180,  173,  159,  174,  175,  169,
          169,  169,  169,  171,  171,  171,  171,  165,  166,  167,
          168,  178,  733,  734,  390,  736,  756,  757,  758,  759,
          760,  761,  762,  764,  701,  140,  141,  142,  144,  145,
          146,  147,  149,  150,  176,  177,  179,  195,  196,  197,
          216,  217,  218,  219,  220,  221,  223,  224,  225,  226,
          236,  237,  270,  271,  272,  424,  425,  426,  181,  182,
          183,  184,  185,  186,  187,  188,  189,  190,  191,  152,
          153,  154,  155,  172,  156,  193,  157,  158,  160,  194,
          161,  162,  192,  135,  163,  164,  446,  446,  446,  446,
          446,  446,  446,  446,  446,  446,  446,  446,  446,  446,
          446,  446,  446,  446,  434,  435,  437,  440,  466,  468,
          469,  269,  545,  545,  545,  261,  312,  392,  392,  392,
          392,  392,  392,  458,  485,  318,  392,  392,  392,  392,
          392,  392,  392,  392,  392,  392,  392,  392,  392,  392,
          392,  780,  780,  780,  649,  649,  649,  400,  400,  332,
          649,  544,  544,  544,  433,  436,  441,  396,  396,  396,
          863,  613,  613,  608,  614,  489,  447,  447,  447,  447,
          447,  447,  447,  447,  447,  447,  447,  447,  447,  447,
          447,  447,  447,  447,  297,  783,  412,  782,  311,  311,
          311,  678,  844,  844,  844,  745,  358,  393,  393,  393,
          393,  393,  393,  862,  862,  862,  393,  393,  393,  393,
          393,  393,  393,  393,  393,  393,  393,  393,  393,  393,
          393,  395,  395,  395,  288,  288,  288,  288,  288,  288,
          586,  324,  587,  288,  288,  288,  288,  288,  288,  288,
          288,  288,  288,  288,  288,  288,  288,  288,  289,  289,
          289,    5,  360,  460,  513,   14,  467,    6,    7,  328,
          328,  328,    8,    9,   10,   15,   16,   11,   17,   12,
           18,   13,  790,  789,  319,    1,    2,  552,  330,  331,
          419,  419,  419,  414,  455,  295,  657,  595,  529,  416,
          416,  391,  394,  325,  327,  453,  456,  465,  333,  479,
          480,  482,  336,  504,  813,  813,  813,  813,  813,  813,
          813,  813,  813,  813,  813,  813,  813,  813,  813,  813,
          813,  813,  559,  665,  621,  815,  816,  558,  663,  622,
          645,  831,  499,  690,  688,  647,  829,  689,  686,  543,
          543,  543,  699,  228,    0,    0,  826,    0,    0,    0,
            0,    0,    0,  298,  299,    0,    0,  632,  619,  617,
          617,  615,  617,  520,  397,  639,  635,  313,    0,  406,
          878,  878,    0,    0,    0,    0,  461,    0,  881,  878,
            0,    0,  251,  488,    0,    0,  503,  510,    0,    0,
          881,  881
    );

    private static $yygcheck = array(
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   35,   35,   35,   35,
           35,   35,   35,   35,   35,   35,   35,   35,   35,   35,
           35,   35,   35,   35,   47,   47,   47,   47,   47,   47,
           47,   77,    8,    8,    8,   77,   26,   35,   35,   35,
           35,   35,   35,   21,   21,    4,   35,   35,   35,   35,
           35,   35,   35,   35,   35,   35,   35,   35,   35,   35,
           35,   35,   35,   35,   35,   35,   35,    4,    4,   58,
           35,    7,    7,    7,   76,   76,   76,   74,   74,   74,
           85,   35,   35,   35,   35,   35,   68,   68,   68,   68,
           68,   68,   68,   68,   68,   68,   68,   68,   68,   68,
           68,   68,   68,   68,   43,    4,    4,    4,   70,   70,
           70,   57,   71,   71,   71,   59,   59,   68,   68,   68,
           68,   68,   68,   71,   71,   71,   68,   68,   68,   68,
           68,   68,   68,   68,   68,   68,   68,   68,   68,   68,
           68,   68,   68,   68,   69,   69,   69,   69,   69,   69,
           29,   27,   29,   69,   69,   69,   69,   69,   69,   69,
           69,   69,   69,   69,   69,   69,   69,   69,   69,   69,
           69,   13,   46,   36,   36,   13,   42,   13,   13,   69,
           69,   69,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   75,   75,   69,    2,    2,   11,   69,   69,
           28,   28,   28,   28,   28,   28,   49,   34,   44,   28,
           28,   28,   28,   28,   28,   28,   28,   28,   28,   28,
           28,   28,   28,   28,   78,   78,   78,   78,   78,   78,
           78,   78,   78,   78,   78,   78,   78,   78,   78,   78,
           78,   78,   12,   12,   12,   12,   12,   12,   12,   12,
           12,   12,   12,   12,   12,   12,   12,   12,   12,    6,
            6,    6,   60,   40,   -1,   -1,   82,   -1,   -1,   -1,
           -1,   -1,   -1,   43,   43,   -1,   -1,    6,    6,    6,
            6,    6,    6,    6,    6,    6,    6,   30,   -1,   30,
           87,   87,   -1,   -1,   -1,   -1,   30,   -1,   87,   87,
           -1,   -1,   30,   30,   -1,   -1,   30,   30,   -1,   -1,
           87,   87
    );

    private static $yygbase = array(
            0,    0, -221,    0, -133,    0,  358,  170,  131,    0,
            0,   -4,  143, -244,    0,  -29,    0,    0,    0,    0,
            0,   94,    0,    0,    0,    0,   85,   12,   71,  228,
           43,    0,    0,    0,  -50,  -92,   16,    0,    0,    0,
         -160,    0,    2, -151,    1,    0,   10,   93,    0,    3,
            0,    0,    0,    0,    0,    0,    0,  -24,  -61,  -39,
            4,    0,    0,    0,    0,    0,    0,    0,  -12,   15,
          -45,  -41,    0,    0,  -76,   27,  121, -132,  126,    0,
            0,    0,    5,    0,    0,  -51,    0,  130,    0
    );

    private static $yygdefault = array(
        -32768,  365,    3,  538,  781,  385,  562,  563,  564,  314,
          309,  553,  475,    4,  560,  136,  305,  567,  306,  495,
          569,  408,  571,  572,  315,  316,  409,  323,  222,  585,
          497,  322,  588,  357,  594,  310,  443,  386,  352,  457,
          227,  417,  451,  296,  532,  444,  353,  428,  429,  658,
          666,  362,  335,  334,  478,  670,  235,  677,  326,  347,
          700,  763,  765,  420,  403,  473,  337,  835,  387,  285,
          286,  388,  785,  290,  834,  427,  431,  260,  822,  476,
          820,  361,  869,  833,  287,  864,  351,  880,  454
    );

    private static $yylhs = array(
            0,    1,    2,    2,    4,    5,    5,    3,    3,    3,
            3,    3,    3,    3,    3,    3,    9,    9,   11,   11,
           11,   11,   10,   10,   13,   13,   14,   14,   14,   14,
            6,    6,    6,    6,    6,    6,    6,    6,    6,    6,
            6,    6,    6,    6,    6,    6,    6,    6,    6,    6,
            6,    6,    6,    6,    6,    6,    6,    6,   33,   33,
           34,   27,   27,   30,   30,    7,    8,    8,   37,   37,
           37,   38,   38,   41,   41,   39,   39,   42,   42,   22,
           22,   29,   29,   32,   32,   31,   31,   23,   23,   23,
           23,   43,   43,   43,   44,   44,   20,   20,   16,   16,
           18,   18,   17,   17,   19,   19,   36,   36,   45,   45,
           45,   45,   46,   46,   46,   47,   47,   48,   48,   48,
           48,   24,   24,   49,   49,   49,   25,   25,   25,   25,
           40,   40,   50,   50,   50,   55,   55,   51,   51,   54,
           54,   56,   56,   57,   57,   57,   57,   57,   57,   52,
           52,   52,   52,   53,   53,   26,   26,   21,   21,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   15,   15,   15,   15,   15,   15,
           15,   15,   15,   15,   65,   65,   66,   66,   67,   67,
           67,   67,   67,   68,   68,   35,   35,   35,   59,   59,
           72,   72,   73,   73,   73,   73,   73,   61,   61,   61,
           64,   64,   64,   60,   60,   78,   78,   78,   78,   78,
           78,   78,   78,   78,   78,   78,   78,   12,   12,   12,
           12,   12,   12,   62,   62,   62,   62,   62,   62,   79,
           79,   82,   82,   81,   81,   81,   81,   28,   28,   28,
           83,   83,   83,   84,   84,   84,   71,   71,   74,   74,
           74,   74,   70,   70,   70,   70,   70,   70,   69,   69,
           69,   69,   76,   76,   75,   75,   75,   58,   58,   85,
           85,   85,   63,   63,   86,   86,   86,   86,   86,   86,
           86,   86,   77,   77,   77,   77,   87,   87,   87,   87,
           87,   87,   88,   88,   88,   80,   80
    );

    private static $yylen = array(
            1,    1,    2,    0,    1,    1,    3,    1,    1,    1,
            1,    3,    5,    4,    3,    2,    3,    1,    1,    3,
            2,    4,    5,    4,    2,    0,    1,    1,    1,    1,
            3,    7,   10,    5,    7,    9,    5,    2,    3,    2,
            3,    2,    3,    3,    3,    3,    1,    2,    5,    7,
            8,   10,    5,    1,    5,    3,    3,    2,    1,    2,
            8,    1,    3,    0,    1,    9,    7,    6,    1,    2,
            2,    0,    2,    0,    2,    0,    2,    1,    3,    1,
            4,    1,    4,    1,    4,    3,    5,    3,    4,    4,
            5,    0,    5,    4,    1,    1,    1,    4,    0,    6,
            0,    7,    0,    2,    0,    3,    1,    0,    3,    5,
            5,    7,    0,    1,    1,    1,    0,    1,    2,    3,
            4,    3,    1,    1,    2,    4,    3,    5,    1,    3,
            2,    0,    3,    2,    8,    1,    3,    1,    1,    0,
            1,    1,    2,    1,    1,    1,    1,    1,    1,    3,
            5,    1,    3,    5,    4,    3,    1,    0,    1,    1,
            6,    3,    4,    6,    3,    2,    3,    3,    3,    3,
            3,    3,    3,    3,    3,    3,    3,    2,    2,    2,
            2,    3,    3,    3,    3,    3,    3,    3,    3,    3,
            3,    3,    3,    3,    3,    3,    3,    2,    2,    2,
            2,    3,    3,    3,    3,    3,    3,    3,    3,    3,
            3,    5,    4,    4,    4,    2,    2,    4,    2,    2,
            2,    2,    2,    2,    2,    2,    2,    2,    2,    1,
            4,    3,    2,    9,    0,    4,    4,    2,    4,    6,
            6,    4,    4,    1,    1,    1,    3,    2,    1,    1,
            1,    1,    0,    3,    3,    4,    4,    0,    2,    3,
            0,    1,    1,    0,    3,    1,    1,    1,    1,    1,
            1,    1,    1,    1,    1,    3,    2,    1,    1,    2,
            2,    4,    3,    1,    1,    1,    1,    3,    3,    0,
            2,    0,    1,    5,    3,    3,    1,    1,    1,    1,
            1,    4,    6,    3,    4,    4,    1,    2,    1,    4,
            4,    1,    3,    3,    6,    6,    4,    4,    4,    4,
            1,    4,    0,    1,    1,    3,    1,    3,    1,    1,
            4,    0,    0,    2,    5,    3,    3,    1,    6,    4,
            4,    2,    2,    2,    1,    2,    1,    4,    3,    3,
            6,    3,    1,    1,    1,    3,    3
    );

    /* Debug Mode */
    protected function yyprintln($msg) {
        echo $msg, "\n";
    }

    private function YYTRACE_NEWSTATE($state, $sym) {
        $this->yyprintln(
            '% State ' . $state
          . ', Lookahead ' . ($sym < 0 ? '--none--' : self::$yyterminals[$sym])
        );
    }

    private function YYTRACE_READ($sym) {
        $this->yyprintln('% Reading ' . self::$yyterminals[$sym]);
    }

    private function YYTRACE_SHIFT($sym) {
        $this->yyprintln('% Shift ' . self::$yyterminals[$sym]);
    }

    private function YYTRACE_ACCEPT() {
        $this->yyprintln('% Accepted.');
    }

    private function YYTRACE_REDUCE($n) {
        $this->yyprintln('% Reduce by (' . $n . ') ' . self::$yyproduction[$n]);
    }

    private function YYTRACE_POP($state) {
        $this->yyprintln('% Recovering, uncovers state ' . $state);
    }

    private function YYTRACE_DISCARD($sym) {
        $this->yyprintln('% Discard ' . self::$yyterminals[$sym]);
    }

    protected $yyval;
    protected $yyastk;
    protected $yysp;
    protected $yyaccept;
    protected $lexer;

    /**
     * Parses PHP code into a node tree and prints out debugging information.
     *
     * @param PHPParser_Lexer $lexer A lexer
     *
     * @return array Array of statements
     */
    public function parse(PHPParser_Lexer $lexer) {
        $this->lexer  = $lexer;

        $this->yysp   = 0;                   // Stack pos
        $yysstk       = array($yystate = 0); // State stack
        $this->yyastk = array();             // AST   stack (?)
        $yylstk       = array($yyline  = 1); // Line  stack
        $yydstk       = array($yyDC = null); // Doc comment stack

        $yychar       = -1;

        for (;;) {
            $this->YYTRACE_NEWSTATE($yystate, $yychar);
            if (self::$yybase[$yystate] == 0) {
                $yyn = self::$yydefault[$yystate];
            } else {
                if ($yychar < 0) {
                    if (($yychar = $lexer->lex($yylval, $yyline, $yyDC)) < 0)
                        $yychar = 0;
                    $yychar = $yychar < self::YYMAXLEX ?
                        self::$yytranslate[$yychar] : self::YYBADCH;
                    $yylstk[$this->yysp] = $yyline;
                    $yydstk[$this->yysp] = $yyDC;
                    $this->YYTRACE_READ($yychar);
                }
                if ((($yyn = self::$yybase[$yystate] + $yychar) >= 0
                     && $yyn < self::YYLAST && self::$yycheck[$yyn] == $yychar
                     || ($yystate < self::YY2TBLSTATE
                        && ($yyn = self::$yybase[$yystate + self::YYNLSTATES]
                            + $yychar) >= 0
                        && $yyn < self::YYLAST
                        && self::$yycheck[$yyn] == $yychar))
                    && ($yyn = self::$yyaction[$yyn]) != self::YYDEFAULT) {
                    /*
                     * >= YYNLSTATE: shift and reduce
                     * > 0: shift
                     * = 0: accept
                     * < 0: reduce
                     * = -YYUNEXPECTED: error
                     */
                    if ($yyn > 0) {
                        /* shift */
                        $this->YYTRACE_SHIFT($yychar);
                        ++$this->yysp;

                        $yysstk[$this->yysp]       = $yystate = $yyn;
                        $this->yyastk[$this->yysp] = $yylval;
                        $yylstk[$this->yysp]       = $yyline;
                        $yydstk[$this->yysp]       = $yyDC;
                        $yychar = -1;

                        if ($yyn < self::YYNLSTATES)
                            continue;

                        /* $yyn >= YYNLSTATES means shift-and-reduce */
                        $yyn -= self::YYNLSTATES;
                    } else {
                        $yyn = -$yyn;
                    }
                } else {
                    $yyn = self::$yydefault[$yystate];
                }
            }

            for (;;) {
                /* reduce/error */
                if ($yyn == 0) {
                    /* accept */
                    $this->YYTRACE_ACCEPT();
                    return $this->yyval;
                } elseif ($yyn != self::YYUNEXPECTED) {
                    /* reduce */
                    $this->YYTRACE_REDUCE($yyn);
                    try {
                        $this->{'yyn' . $yyn}(
                            $yylstk[$this->yysp - self::$yylen[$yyn]],
                            $yydstk[$this->yysp - self::$yylen[$yyn]]
                        );
                    } catch (PHPParser_Error $e) {
                        $e->setRawLine($yyline);

                        throw $e;
                    }

                    /* Goto - shift nonterminal */
                    $this->yysp -= self::$yylen[$yyn];
                    $yyn = self::$yylhs[$yyn];
                    if (($yyp = self::$yygbase[$yyn] + $yysstk[$this->yysp]) >= 0
                         && $yyp < self::YYGLAST
                         && self::$yygcheck[$yyp] == $yyn) {
                        $yystate = self::$yygoto[$yyp];
                    } else {
                        $yystate = self::$yygdefault[$yyn];
                    }

                    ++$this->yysp;

                    $yysstk[$this->yysp] = $yystate;
                    $this->yyastk[$this->yysp] = $this->yyval;
                    $yylstk[$this->yysp]       = $yyline;
                    $yydstk[$this->yysp]       = $yyDC;
                } else {
                    /* error */
                    throw new PHPParser_Error(
                        'Unexpected token ' . self::$yyterminals[$yychar],
                        $yyline
                    );
                }

                if ($yystate < self::YYNLSTATES)
                    break;
                /* >= YYNLSTATES means shift-and-reduce */
                $yyn = $yystate - self::YYNLSTATES;
            }
        }
    }

    private function yyn0() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn1($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Namespace::postprocess($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn2($line, $docComment) {
         if (is_array($this->yyastk[$this->yysp-(2-2)])) { $this->yyval = array_merge($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); } else { $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; }; 
    }

    private function yyn3($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn4($line, $docComment) {
         $this->yyval = new PHPParser_Node_Name(array('parts' => $this->yyastk[$this->yysp-(1-1)], 'type' => PHPParser_Node_Name::NORMAL), $line, $docComment); 
    }

    private function yyn5($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn6($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn7($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn8($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn9($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn10($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_HaltCompiler(array('remaining' => $this->lexer->handleHaltCompiler()), $line, $docComment); 
    }

    private function yyn11($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Namespace(array('name' => $this->yyastk[$this->yysp-(3-2)], 'stmts' => null), $line, $docComment); 
    }

    private function yyn12($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Namespace(array('name' => $this->yyastk[$this->yysp-(5-2)], 'stmts' => $this->yyastk[$this->yysp-(5-4)]), $line, $docComment); 
    }

    private function yyn13($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Namespace(array('name' => null, 'stmts' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn14($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Use(array('uses' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn15($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Const(array('consts' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment); 
    }

    private function yyn16($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn17($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn18($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_UseUse(array('name' => $this->yyastk[$this->yysp-(1-1)], 'alias' => null), $line, $docComment); 
    }

    private function yyn19($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_UseUse(array('name' => $this->yyastk[$this->yysp-(3-1)], 'alias' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn20($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_UseUse(array('name' => $this->yyastk[$this->yysp-(2-2)], 'alias' => null), $line, $docComment); 
    }

    private function yyn21($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_UseUse(array('name' => $this->yyastk[$this->yysp-(4-2)], 'alias' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); 
    }

    private function yyn22($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_ConstConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn23($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_ConstConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment)); 
    }

    private function yyn24($line, $docComment) {
         if (is_array($this->yyastk[$this->yysp-(2-2)])) { $this->yyval = array_merge($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); } else { $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; }; 
    }

    private function yyn25($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn26($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn27($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn28($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn29($line, $docComment) {
         throw new PHPParser_Error('__halt_compiler() can only be used from the outermost scope'); 
    }

    private function yyn30($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn31($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_If(array('cond' => $this->yyastk[$this->yysp-(7-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-5)]) ? $this->yyastk[$this->yysp-(7-5)] : array($this->yyastk[$this->yysp-(7-5)]), 'elseifList' => $this->yyastk[$this->yysp-(7-6)], 'else' => $this->yyastk[$this->yysp-(7-7)]), $line, $docComment); 
    }

    private function yyn32($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_If(array('cond' => $this->yyastk[$this->yysp-(10-3)], 'stmts' => $this->yyastk[$this->yysp-(10-6)], 'elseifList' => $this->yyastk[$this->yysp-(10-7)], 'else' => $this->yyastk[$this->yysp-(10-8)]), $line, $docComment); 
    }

    private function yyn33($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_While(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)])), $line, $docComment); 
    }

    private function yyn34($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Do(array('stmts' => is_array($this->yyastk[$this->yysp-(7-2)]) ? $this->yyastk[$this->yysp-(7-2)] : array($this->yyastk[$this->yysp-(7-2)]), 'cond' => $this->yyastk[$this->yysp-(7-5)]), $line, $docComment); 
    }

    private function yyn35($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_For(array('init' => $this->yyastk[$this->yysp-(9-3)], 'cond' => $this->yyastk[$this->yysp-(9-5)], 'loop' => $this->yyastk[$this->yysp-(9-7)], 'stmts' => is_array($this->yyastk[$this->yysp-(9-9)]) ? $this->yyastk[$this->yysp-(9-9)] : array($this->yyastk[$this->yysp-(9-9)])), $line, $docComment); 
    }

    private function yyn36($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Switch(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'caseList' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); 
    }

    private function yyn37($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Break(array('num' => null), $line, $docComment); 
    }

    private function yyn38($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Break(array('num' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn39($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Continue(array('num' => null), $line, $docComment); 
    }

    private function yyn40($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Continue(array('num' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn41($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Return(array('expr' => null), $line, $docComment); 
    }

    private function yyn42($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Return(array('expr' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn43($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Global(array('vars' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn44($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Static(array('vars' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn45($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Echo(array('exprs' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn46($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_InlineHTML(array('value' => $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn47($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn48($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Unset(array('vars' => $this->yyastk[$this->yysp-(5-3)]), $line, $docComment); 
    }

    private function yyn49($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(7-3)], 'keyVar' => null, 'byRef' => false, 'valueVar' => $this->yyastk[$this->yysp-(7-5)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-7)]) ? $this->yyastk[$this->yysp-(7-7)] : array($this->yyastk[$this->yysp-(7-7)])), $line, $docComment); 
    }

    private function yyn50($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(8-3)], 'keyVar' => null, 'byRef' => true, 'valueVar' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => is_array($this->yyastk[$this->yysp-(8-8)]) ? $this->yyastk[$this->yysp-(8-8)] : array($this->yyastk[$this->yysp-(8-8)])), $line, $docComment); 
    }

    private function yyn51($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(10-3)], 'keyVar' => $this->yyastk[$this->yysp-(10-5)], 'byRef' => $this->yyastk[$this->yysp-(10-7)], 'valueVar' => $this->yyastk[$this->yysp-(10-8)], 'stmts' => is_array($this->yyastk[$this->yysp-(10-10)]) ? $this->yyastk[$this->yysp-(10-10)] : array($this->yyastk[$this->yysp-(10-10)])), $line, $docComment); 
    }

    private function yyn52($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Declare(array('declares' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)])), $line, $docComment); 
    }

    private function yyn53($line, $docComment) {
         $this->yyval = array(); /* means: no statement */ 
    }

    private function yyn54($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_TryCatch(array('stmts' => $this->yyastk[$this->yysp-(5-3)], 'catches' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); 
    }

    private function yyn55($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Throw(array('expr' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn56($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Goto(array('name' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn57($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Label(array('name' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment); 
    }

    private function yyn58($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn59($line, $docComment) {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn60($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Catch(array('type' => $this->yyastk[$this->yysp-(8-3)], 'var' => substr($this->yyastk[$this->yysp-(8-4)], 1), 'stmts' => $this->yyastk[$this->yysp-(8-7)]), $line, $docComment); 
    }

    private function yyn61($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn62($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn63($line, $docComment) {
         $this->yyval = false; 
    }

    private function yyn64($line, $docComment) {
         $this->yyval = true; 
    }

    private function yyn65($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Func(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'name' => $this->yyastk[$this->yysp-(9-3)], 'params' => $this->yyastk[$this->yysp-(9-5)], 'stmts' => $this->yyastk[$this->yysp-(9-8)]), $line, $docComment); 
    }

    private function yyn66($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Class(array('type' => $this->yyastk[$this->yysp-(7-1)], 'name' => $this->yyastk[$this->yysp-(7-2)], 'extends' => $this->yyastk[$this->yysp-(7-3)], 'implements' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-6)]), $line, $docComment); 
    }

    private function yyn67($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Interface(array('name' => $this->yyastk[$this->yysp-(6-2)], 'extends' => $this->yyastk[$this->yysp-(6-3)], 'stmts' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn68($line, $docComment) {
         $this->yyval = 0; 
    }

    private function yyn69($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT; 
    }

    private function yyn70($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_FINAL; 
    }

    private function yyn71($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn72($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn73($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn74($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn75($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn76($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn77($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn78($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn79($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn80($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn81($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn82($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn83($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn84($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn85($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_DeclareDeclare(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment)); 
    }

    private function yyn86($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_DeclareDeclare(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn87($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn88($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn89($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn90($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(5-3)]; 
    }

    private function yyn91($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn92($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_Case(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn93($line, $docComment) {
         $this->yyastk[$this->yysp-(4-1)][] = new PHPParser_Node_Stmt_Case(array('cond' => null, 'stmts' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn94() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn95() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn96($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn97($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn98($line, $docComment) {
         $this->yyval = array();
    }

    private function yyn99($line, $docComment) {
         $this->yyastk[$this->yysp-(6-1)][] = new PHPParser_Node_Stmt_ElseIf(array('cond' => $this->yyastk[$this->yysp-(6-4)], 'stmts' => is_array($this->yyastk[$this->yysp-(6-6)]) ? $this->yyastk[$this->yysp-(6-6)] : array($this->yyastk[$this->yysp-(6-6)])), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(6-1)]; 
    }

    private function yyn100($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn101($line, $docComment) {
         $this->yyastk[$this->yysp-(7-1)][] = new PHPParser_Node_Stmt_ElseIf(array('cond' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-7)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(7-1)]; 
    }

    private function yyn102($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn103($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Else(array('stmts' => is_array($this->yyastk[$this->yysp-(2-2)]) ? $this->yyastk[$this->yysp-(2-2)] : array($this->yyastk[$this->yysp-(2-2)])), $line, $docComment); 
    }

    private function yyn104($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn105($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Else(array('stmts' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn106($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn107($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn108($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(3-2)], 'default' => null), $line, $docComment)); 
    }

    private function yyn109($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(5-1)], 'name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-2)], 'default' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment)); 
    }

    private function yyn110($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(5-3)], 'name' => substr($this->yyastk[$this->yysp-(5-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-4)], 'default' => null), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn111($line, $docComment) {
         $this->yyastk[$this->yysp-(7-1)][] = new PHPParser_Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(7-3)], 'name' => substr($this->yyastk[$this->yysp-(7-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(7-4)], 'default' => $this->yyastk[$this->yysp-(7-7)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(7-1)]; 
    }

    private function yyn112($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn113($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn114($line, $docComment) {
         $this->yyval = 'array'; 
    }

    private function yyn115($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn116($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn117($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false), $line, $docComment)); 
    }

    private function yyn118($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true), $line, $docComment)); 
    }

    private function yyn119($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = new PHPParser_Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn120($line, $docComment) {
         $this->yyastk[$this->yysp-(4-1)][] = new PHPParser_Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn121($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn122($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn123($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)), $line, $docComment); 
    }

    private function yyn124($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn125($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn126($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = new PHPParser_Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn127($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn128($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null), $line, $docComment)); 
    }

    private function yyn129($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment)); 
    }

    private function yyn130($line, $docComment) {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn131($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn132($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_Property(array('type' => $this->yyastk[$this->yysp-(3-1)], 'props' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn133($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_ClassConst(array('consts' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment); 
    }

    private function yyn134($line, $docComment) {
         $this->yyval = new PHPParser_Node_Stmt_ClassMethod(array('type' => $this->yyastk[$this->yysp-(8-1)], 'byRef' => $this->yyastk[$this->yysp-(8-3)], 'name' => $this->yyastk[$this->yysp-(8-4)], 'params' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => $this->yyastk[$this->yysp-(8-8)]), $line, $docComment); 
    }

    private function yyn135($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn136($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn137($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn138($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn139($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn140($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn141($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn142($line, $docComment) {
         PHPParser_Node_Stmt_Class::verifyModifier($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)] | $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn143($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn144($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED; 
    }

    private function yyn145($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE; 
    }

    private function yyn146($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_STATIC; 
    }

    private function yyn147($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT; 
    }

    private function yyn148($line, $docComment) {
         $this->yyval = PHPParser_Node_Stmt_Class::MODIFIER_FINAL; 
    }

    private function yyn149($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = new PHPParser_Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn150($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn151($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null), $line, $docComment)); 
    }

    private function yyn152($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment)); 
    }

    private function yyn153($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Stmt_ClassConstConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn154($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Stmt_ClassConstConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment)); 
    }

    private function yyn155($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn156($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn157($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn158($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn159($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn160($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_List(array('assignList' => $this->yyastk[$this->yysp-(6-3)], 'expr' => $this->yyastk[$this->yysp-(6-6)]), $line, $docComment); 
    }

    private function yyn161($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Assign(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn162($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignRef(array('var' => $this->yyastk[$this->yysp-(4-1)], 'refVar' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); 
    }

    private function yyn163($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Assign(array('var' => $this->yyastk[$this->yysp-(6-1)], 'expr' => new PHPParser_Node_Expr_New(array('class' => $this->yyastk[$this->yysp-(6-5)], 'args' => $this->yyastk[$this->yysp-(6-6)]), $line, $docComment)), $line, $docComment); 
    }

    private function yyn164($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_New(array('class' => $this->yyastk[$this->yysp-(3-2)], 'args' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn165($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Clone(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn166($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignPlus(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn167($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignMinus(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn168($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignMul(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn169($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignDiv(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn170($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignConcat(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn171($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignMod(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn172($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignBinAnd(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn173($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignBinOr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn174($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignBinXor(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn175($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignShiftLeft(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn176($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_AssignShiftRight(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn177($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PostInc(array('var' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment); 
    }

    private function yyn178($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PreInc(array('var' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn179($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PostDec(array('var' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment); 
    }

    private function yyn180($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PreDec(array('var' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn181($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BooleanOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn182($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BooleanAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn183($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_LogicalOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn184($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_LogicalAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn185($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_LogicalXor(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn186($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BinaryOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn187($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BinaryAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn188($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BinaryXor(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn189($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Concat(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn190($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Plus(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn191($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Minus(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn192($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Mul(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn193($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Div(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn194($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Mod(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn195($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ShiftLeft(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn196($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ShiftRight(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn197($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_UnaryPlus(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn198($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_UnaryMinus(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn199($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BooleanNot(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn200($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BinaryNot(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn201($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Identical(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn202($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_NotIdentical(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn203($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Equal(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn204($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_NotEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn205($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Smaller(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn206($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_SmallerOrEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn207($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Greater(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn208($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_GreaterOrEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn209($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Instanceof(array('expr' => $this->yyastk[$this->yysp-(3-1)], 'class' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn210($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn211($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Ternary(array('cond' => $this->yyastk[$this->yysp-(5-1)], 'if' => $this->yyastk[$this->yysp-(5-3)], 'else' => $this->yyastk[$this->yysp-(5-5)]), $line, $docComment); 
    }

    private function yyn212($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Ternary(array('cond' => $this->yyastk[$this->yysp-(4-1)], 'if' => null, 'else' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); 
    }

    private function yyn213($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Isset(array('vars' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn214($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Empty(array('var' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn215($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => PHPParser_Node_Expr_Include::TYPE_INCLUDE), $line, $docComment); 
    }

    private function yyn216($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE), $line, $docComment); 
    }

    private function yyn217($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Eval(array('expr' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn218($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => PHPParser_Node_Expr_Include::TYPE_REQUIRE), $line, $docComment); 
    }

    private function yyn219($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE), $line, $docComment); 
    }

    private function yyn220($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_IntCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn221($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_DoubleCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn222($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StringCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn223($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn224($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ObjectCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn225($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_BoolCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn226($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_UnsetCast(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn227($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Exit(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn228($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ErrorSuppress(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn229($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn230($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Array(array('items' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn231($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ShellExec(array('parts' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn232($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Print(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn233($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_LambdaFunc(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'params' => $this->yyastk[$this->yysp-(9-4)], 'useVars' => $this->yyastk[$this->yysp-(9-6)], 'stmts' => $this->yyastk[$this->yysp-(9-8)]), $line, $docComment); 
    }

    private function yyn234($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn235($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn236($line, $docComment) {
         $this->yyastk[$this->yysp-(4-1)][] = new PHPParser_Node_Expr_LambdaFuncUse(array('var' => substr($this->yyastk[$this->yysp-(4-4)], 1), 'byRef' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn237($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_LambdaFuncUse(array('var' => substr($this->yyastk[$this->yysp-(2-2)], 1), 'byRef' => $this->yyastk[$this->yysp-(2-1)]), $line, $docComment)); 
    }

    private function yyn238($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_FuncCall(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn239($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn240($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn241($line, $docComment) {
        
            if ($this->yyastk[$this->yysp-(4-1)] instanceof PHPParser_Node_Expr_StaticPropertyFetch) {
                $this->yyval = new PHPParser_Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(4-1)]->class, 'func' => $this->yyastk[$this->yysp-(4-1)]->name, 'args' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment);
            } elseif ($this->yyastk[$this->yysp-(4-1)] instanceof PHPParser_Node_Expr_ArrayDimFetch) {
                $tmp = $this->yyastk[$this->yysp-(4-1)];
                while ($tmp->var instanceof PHPParser_Node_Expr_ArrayDimFetch) {
                    $tmp = $tmp->var;
                }

                $this->yyval = new PHPParser_Node_Expr_StaticCall(array('class' => $tmp->var->class, 'func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment);
                $tmp->var = new PHPParser_Node_Variable(array('name' => $tmp->var->name), $line, $docComment);
            } else {
                throw new Exception;
            }
          
    }

    private function yyn242($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_FuncCall(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn243($line, $docComment) {
         $this->yyval = 'static'; 
    }

    private function yyn244($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn245($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn246($line, $docComment) {
         $this->yyastk[$this->yysp-(3-3)]->type = PHPParser_Node_Name::RELATIVE; $this->yyval = $this->yyastk[$this->yysp-(3-3)]; 
    }

    private function yyn247($line, $docComment) {
         $this->yyastk[$this->yysp-(2-2)]->type = PHPParser_Node_Name::FULLY_QUALIFIED; $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn248($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn249($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn250($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn251($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn252() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn253($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PropertyFetch(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn254($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PropertyFetch(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn255($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn256($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn257($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn258($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn259($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn260($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn261($line, $docComment) {
         $this->yyval = array(PHPParser_Node_Scalar_String::parseEscapeSequences($this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn262($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn263($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn264($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn265($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_LNumber(array('value' => (int) $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn266($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_DNumber(array('value' => (double) $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn267($line, $docComment) {
         $this->yyval = PHPParser_Node_Scalar_String::create($this->yyastk[$this->yysp-(1-1)], $line); 
    }

    private function yyn268($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_LineConst(array(), $line, $docComment); 
    }

    private function yyn269($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_FileConst(array(), $line, $docComment); 
    }

    private function yyn270($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_DirConst(array(), $line, $docComment); 
    }

    private function yyn271($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_ClassConst(array(), $line, $docComment); 
    }

    private function yyn272($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_MethodConst(array(), $line, $docComment); 
    }

    private function yyn273($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_FuncConst(array(), $line, $docComment); 
    }

    private function yyn274($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_NSConst(array(), $line, $docComment); 
    }

    private function yyn275($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_String(array('value' => PHPParser_Node_Scalar_String::parseEscapeSequences($this->yyastk[$this->yysp-(3-2)])), $line, $docComment); 
    }

    private function yyn276($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_String(array('value' => ''), $line, $docComment); 
    }

    private function yyn277($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn278($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ConstFetch(array('name' => $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn279($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_UnaryPlus(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn280($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_UnaryMinus(array('expr' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn281($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_Array(array('items' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn282($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn283($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_String(array('value' => $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn284($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn285($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ConstFetch(array('name' => $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn286($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn287($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_Encapsed(array('parts' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn288($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_Encapsed(array('parts' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn289($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn290($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn291() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn292() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn293($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn294($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn295($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false), $line, $docComment)); 
    }

    private function yyn296($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false), $line, $docComment)); 
    }

    private function yyn297($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn298($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn299($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn300($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn301($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_FuncCall(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn302($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_MethodCall(array('var' => $this->yyastk[$this->yysp-(6-1)], 'name' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn303($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PropertyFetch(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn304($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn305($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn306($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn307($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(2-2)]), $line, $docComment); 
    }

    private function yyn308($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn309($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(4-1)], 'name' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); 
    }

    private function yyn310($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(4-1)], 'name' => $this->yyastk[$this->yysp-(4-4)]), $line, $docComment); 
    }

    private function yyn311($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn312($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1)), $line, $docComment); 
    }

    private function yyn313($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1)), $line, $docComment); 
    }

    private function yyn314($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(6-1)], 'name' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn315($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(6-1)], 'name' => $this->yyastk[$this->yysp-(6-5)]), $line, $docComment); 
    }

    private function yyn316($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn317($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn318($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn319($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn320($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)), $line, $docComment); 
    }

    private function yyn321($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn322($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn323($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn324($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn325($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn326($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn327($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn328($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn329($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn330($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn331($line, $docComment) {
         $this->yyval = null; 
    }

    private function yyn332($line, $docComment) {
         $this->yyval = array(); 
    }

    private function yyn333($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn334($line, $docComment) {
         $this->yyastk[$this->yysp-(5-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn335($line, $docComment) {
         $this->yyastk[$this->yysp-(3-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn336($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false), $line, $docComment)); 
    }

    private function yyn337($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false), $line, $docComment)); 
    }

    private function yyn338($line, $docComment) {
         $this->yyastk[$this->yysp-(6-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(6-3)], 'value' => $this->yyastk[$this->yysp-(6-6)], 'byRef' => true), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(6-1)]; 
    }

    private function yyn339($line, $docComment) {
         $this->yyastk[$this->yysp-(4-1)][] = new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true), $line, $docComment); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn340($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(4-1)], 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true), $line, $docComment)); 
    }

    private function yyn341($line, $docComment) {
         $this->yyval = array(new PHPParser_Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true), $line, $docComment)); 
    }

    private function yyn342($line, $docComment) {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn343($line, $docComment) {
         $this->yyastk[$this->yysp-(2-1)][] = PHPParser_Node_Scalar_String::parseEscapeSequences($this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn344($line, $docComment) {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn345($line, $docComment) {
         $this->yyval = array(PHPParser_Node_Scalar_String::parseEscapeSequences($this->yyastk[$this->yysp-(2-1)]), $this->yyastk[$this->yysp-(2-2)]); 
    }

    private function yyn346($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)), $line, $docComment); 
    }

    private function yyn347($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(4-1)], 1)), $line, $docComment), 'dim' => $this->yyastk[$this->yysp-(4-3)]), $line, $docComment); 
    }

    private function yyn348($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_PropertyFetch(array('var' => new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1)), $line, $docComment), 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn349($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(3-2)]), $line, $docComment); 
    }

    private function yyn350($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ArrayDimFetch(array('var' => new PHPParser_Node_Variable(array('name' => $this->yyastk[$this->yysp-(6-2)]), $line, $docComment), 'dim' => $this->yyastk[$this->yysp-(6-4)]), $line, $docComment); 
    }

    private function yyn351($line, $docComment) {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn352($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_String(array('value' => $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn353($line, $docComment) {
         $this->yyval = new PHPParser_Node_Scalar_LNumber(array('value' => (int) $this->yyastk[$this->yysp-(1-1)]), $line, $docComment); 
    }

    private function yyn354($line, $docComment) {
         $this->yyval = new PHPParser_Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)), $line, $docComment); 
    }

    private function yyn355($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }

    private function yyn356($line, $docComment) {
         $this->yyval = new PHPParser_Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]), $line, $docComment); 
    }
}
