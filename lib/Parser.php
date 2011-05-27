<?php

/* Prototype file of classed PHP parser.
 * Written by Moriyoshi Koizumi, based on the work by Masato Bito.
 * This file is PUBLIC DOMAIN.
 */
class Parser
{
    const YYBADCH      = 145;
    const YYMAXLEX     = 380;
    const YYTERMS      = 145;
    const YYNONTERMS   = 88;
    const YYLAST       = 967;
    const YY2TBLSTATE  = 337;
    const YYGLAST      = 509;
    const YYSTATES     = 762;
    const YYNLSTATES   = 547;
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

    protected $yyval;
    protected $yyastk;
    protected $yysp;
    protected $yyaccept;

    /** Debug mode flag **/
    public $yydebug = true;

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
        "'('",
        "')'",
        "';'",
        "'{'",
        "'}'",
        "'$'",
        "'`'",
        "'\"'",
        "']'"
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
        "top_statement : T_HALT_COMPILER '(' ')' ';'",
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
        "inner_statement : T_HALT_COMPILER '(' ')' ';'",
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
        "statement : T_RETURN expr_without_variable ';'",
        "statement : T_RETURN variable ';'",
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
        "non_empty_function_call_argument_list : expr_without_variable",
        "non_empty_function_call_argument_list : variable",
        "non_empty_function_call_argument_list : '&' variable",
        "non_empty_function_call_argument_list : non_empty_function_call_argument_list ',' expr_without_variable",
        "non_empty_function_call_argument_list : non_empty_function_call_argument_list ',' variable",
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
        "expr_without_variable : T_LIST '(' assignment_list ')' '=' expr",
        "expr_without_variable : variable '=' expr",
        "expr_without_variable : variable '=' '&' variable",
        "expr_without_variable : variable '=' '&' T_NEW class_name_reference ctor_arguments",
        "expr_without_variable : T_NEW class_name_reference ctor_arguments",
        "expr_without_variable : T_CLONE expr",
        "expr_without_variable : variable T_PLUS_EQUAL expr",
        "expr_without_variable : variable T_MINUS_EQUAL expr",
        "expr_without_variable : variable T_MUL_EQUAL expr",
        "expr_without_variable : variable T_DIV_EQUAL expr",
        "expr_without_variable : variable T_CONCAT_EQUAL expr",
        "expr_without_variable : variable T_MOD_EQUAL expr",
        "expr_without_variable : variable T_AND_EQUAL expr",
        "expr_without_variable : variable T_OR_EQUAL expr",
        "expr_without_variable : variable T_XOR_EQUAL expr",
        "expr_without_variable : variable T_SL_EQUAL expr",
        "expr_without_variable : variable T_SR_EQUAL expr",
        "expr_without_variable : variable T_INC",
        "expr_without_variable : T_INC variable",
        "expr_without_variable : variable T_DEC",
        "expr_without_variable : T_DEC variable",
        "expr_without_variable : expr T_BOOLEAN_OR expr",
        "expr_without_variable : expr T_BOOLEAN_AND expr",
        "expr_without_variable : expr T_LOGICAL_OR expr",
        "expr_without_variable : expr T_LOGICAL_AND expr",
        "expr_without_variable : expr T_LOGICAL_XOR expr",
        "expr_without_variable : expr '|' expr",
        "expr_without_variable : expr '&' expr",
        "expr_without_variable : expr '^' expr",
        "expr_without_variable : expr '.' expr",
        "expr_without_variable : expr '+' expr",
        "expr_without_variable : expr '-' expr",
        "expr_without_variable : expr '*' expr",
        "expr_without_variable : expr '/' expr",
        "expr_without_variable : expr '%' expr",
        "expr_without_variable : expr T_SL expr",
        "expr_without_variable : expr T_SR expr",
        "expr_without_variable : '+' expr",
        "expr_without_variable : '-' expr",
        "expr_without_variable : '!' expr",
        "expr_without_variable : '~' expr",
        "expr_without_variable : expr T_IS_IDENTICAL expr",
        "expr_without_variable : expr T_IS_NOT_IDENTICAL expr",
        "expr_without_variable : expr T_IS_EQUAL expr",
        "expr_without_variable : expr T_IS_NOT_EQUAL expr",
        "expr_without_variable : expr '<' expr",
        "expr_without_variable : expr T_IS_SMALLER_OR_EQUAL expr",
        "expr_without_variable : expr '>' expr",
        "expr_without_variable : expr T_IS_GREATER_OR_EQUAL expr",
        "expr_without_variable : expr T_INSTANCEOF class_name_reference",
        "expr_without_variable : '(' expr ')'",
        "expr_without_variable : expr '?' expr ':' expr",
        "expr_without_variable : expr '?' ':' expr",
        "expr_without_variable : T_ISSET '(' variables_list ')'",
        "expr_without_variable : T_EMPTY '(' variable ')'",
        "expr_without_variable : T_INCLUDE expr",
        "expr_without_variable : T_INCLUDE_ONCE expr",
        "expr_without_variable : T_EVAL '(' expr ')'",
        "expr_without_variable : T_REQUIRE expr",
        "expr_without_variable : T_REQUIRE_ONCE expr",
        "expr_without_variable : T_INT_CAST expr",
        "expr_without_variable : T_DOUBLE_CAST expr",
        "expr_without_variable : T_STRING_CAST expr",
        "expr_without_variable : T_ARRAY_CAST expr",
        "expr_without_variable : T_OBJECT_CAST expr",
        "expr_without_variable : T_BOOL_CAST expr",
        "expr_without_variable : T_UNSET_CAST expr",
        "expr_without_variable : T_EXIT exit_expr",
        "expr_without_variable : '@' expr",
        "expr_without_variable : scalar",
        "expr_without_variable : T_ARRAY '(' array_pair_list ')'",
        "expr_without_variable : '`' backticks_expr '`'",
        "expr_without_variable : T_PRINT expr",
        "expr_without_variable : T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars '{' inner_statement_list '}'",
        "lexical_vars : /* empty */",
        "lexical_vars : T_USE '(' lexical_var_list ')'",
        "lexical_var_list : lexical_var_list ',' optional_ref T_VARIABLE",
        "lexical_var_list : optional_ref T_VARIABLE",
        "function_call : name '(' function_call_argument_list ')'",
        "function_call : class_name T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_argument_list ')'",
        "function_call : class_name T_PAAMAYIM_NEKUDOTAYIM variable_without_objects '(' function_call_argument_list ')'",
        "function_call : reference_variable T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_argument_list ')'",
        "function_call : reference_variable T_PAAMAYIM_NEKUDOTAYIM variable_without_objects '(' function_call_argument_list ')'",
        "function_call : variable_without_objects '(' function_call_argument_list ')'",
        "class_name : T_STATIC",
        "class_name : name",
        "name : namespace_name",
        "name : T_NAMESPACE T_NS_SEPARATOR namespace_name",
        "name : T_NS_SEPARATOR namespace_name",
        "class_name_reference : class_name",
        "class_name_reference : dynamic_class_name_reference",
        "dynamic_class_name_reference : dynamic_class_name_reference T_OBJECT_OPERATOR object_property",
        "dynamic_class_name_reference : base_variable",
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
        "expr : variable",
        "expr : expr_without_variable",
        "variable : variable T_OBJECT_OPERATOR object_property '(' function_call_argument_list ')'",
        "variable : variable T_OBJECT_OPERATOR object_property",
        "variable : base_variable",
        "variable : function_call",
        "variable_without_objects : reference_variable",
        "variable_without_objects : '$' reference_variable",
        "base_variable : variable_without_objects",
        "base_variable : class_name T_PAAMAYIM_NEKUDOTAYIM '$' reference_variable",
        "base_variable : reference_variable T_PAAMAYIM_NEKUDOTAYIM '$' reference_variable",
        "base_variable : static_property_with_arrays",
        "static_property_with_arrays : class_name T_PAAMAYIM_NEKUDOTAYIM T_VARIABLE",
        "static_property_with_arrays : reference_variable T_PAAMAYIM_NEKUDOTAYIM '$' '{' expr '}'",
        "static_property_with_arrays : static_property_with_arrays '[' dim_offset ']'",
        "static_property_with_arrays : static_property_with_arrays '{' expr '}'",
        "reference_variable : reference_variable '[' dim_offset ']'",
        "reference_variable : reference_variable '{' expr '}'",
        "reference_variable : T_VARIABLE",
        "reference_variable : '$' '{' expr '}'",
        "dim_offset : /* empty */",
        "dim_offset : expr",
        "object_property : object_dim_list",
        "object_property : variable_without_objects",
        "object_dim_list : object_dim_list '[' dim_offset ']'",
        "object_dim_list : object_dim_list '{' expr '}'",
        "object_dim_list : T_STRING",
        "object_dim_list : '{' expr '}'",
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
          145,  145,  145,   47,  143,  145,  141,   46,   30,  145,
          136,  137,   44,   41,    7,   42,   43,   45,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,   25,  138,
           35,   12,   37,   24,   59,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,   60,  145,  144,   29,  145,  142,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
          145,  145,  145,  139,   28,  140,   49,  145,  145,  145,
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
           55,   56,  366,   57,   58,-32766,-32766,-32766,  247,   59,
          696,  697,  698,  695,  694,  693,-32766,-32766,-32766,  860,
          860,-32766,    0,-32766,-32766,-32766,-32766,-32766,-32767,-32767,
        -32767,-32767,-32766,  673,-32766,-32766,-32766,-32766,-32766,   60,
           61,-32766,-32766,-32766,-32766,   62,-32766,   63,  238,  239,
           64,   65,   66,   67,   68,   69,   70,   71,-32766,  240,
           72,  343,  367,  447,  662, -119,  810,  811,  368,  828,
          860,  262,  594,  204,  812,   51,   26,  369,  530,  370,
         -108,  371,  495,  372,  300,  497,  373,  120,  280,  280,
           38,   39,  374,  346,  344,   40,  376,  344,   73, -122,
          299,  345,  230,  377,  378,  354,  377,  378,  379,  380,
          381,  206,  428,  616,  383,  428,  616,  383,  384,  385,
          816,  817,  818,  813,  814,  257,   82,   83,   84,  121,
          390,  819,  815,  338,   74,  408,  601,  525,  229,  280,
          263,  264,   42,  860,   85,   86,   87,   88,   89,   90,
           91,   92,   93,   94,   95,   96,   97,   98,   99,  100,
          101,  102,  103,  104,  105,   46,  243,  868,-32766,   54,
        -32766,-32766,-32766,  895,  552,  897,  896,  245,-32766,  517,
          248,-32766,-32766,-32766,  503,-32766,-32766,-32766,-32766,-32766,
          344,  423,-32766,   20,  552, -119,-32766,-32766,-32766,  377,
          378,-32766,-32766,  512,-32766,  445,-32766,  793,  428,  616,
          383,-32766,  280,  419,-32766,  591,  298,  552,  487,  355,
          231,-32766,  860,  315,-32766,-32766,-32766,  793,-32766, -122,
        -32766,  688,-32766,  487,   52,-32766,  122, -121,  280,-32766,
        -32766,-32766,-32766,-32766,-32766,-32766,  688,-32766,  126,-32766,
          793,  103,  104,  105,-32766,  243,  390,-32766, -118,  338,
          552,  364,  614,  250,-32766,   45,  562,-32766,-32766,-32766,
          441,-32766,  642,-32766,  886,-32766,  285,  615,-32766,   53,
           80,  242,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  119,
        -32766,  280,-32766,  793,-32766,-32766,-32766,-32766,  283,  265,
        -32766,-32766,  359,  552,-32766,-32766,  251,-32766,  437,  468,
        -32766,-32766,-32766,  117,-32766,  683,-32766,  246,-32766,  662,
           21,-32766,  100,  101,  102,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,  860,-32766,  682,-32766,  793,  860,  273,  854,
        -32766,  285,  832,-32766,  592,  542,  552,  561,  210,  253,
        -32766,  209,  821,-32766,-32766,-32766,  426,-32766,  118,-32766,
          207,-32766,  821,  201,-32766,  886,  205, -121,-32766,-32766,
        -32766,-32766,-32766,-32766,-32766,  202,-32766,  284,-32766,  793,
          340,  494,   27,-32766,  527,  643,-32766,  129, -118,  552,
          194,  784,  249,-32766,  504,  505,-32766,-32766,-32766,  200,
        -32766,  280,-32766,  199,-32766,  195,  405,-32766,  301,  558,
          528,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  833,-32766,
          196,-32766,  793,  130,  305,  537,-32766,  685,  546,-32766,
          243,  533,  552,  612,  532,  244,-32766,  541,  605,-32766,
        -32766,-32766,  545,-32766,  125,-32766,  124,-32766,  516,  552,
        -32766,  281,  785,  520,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,  786,-32766,  282,-32766,  793,  513,  501,  465,-32766,
          886,  827,-32766,  411,  444,  552,-32766,-32766,-32766,-32766,
          568,  891,-32766,-32766,-32766,  436,-32766,  363,-32766,  417,
        -32766,  429,-32766,-32766,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,-32766,  415,-32766,  553,-32766,  793,  566,
          414,  400,-32766,  349,  350,-32766,  399, -140,  552,-32766,
        -32766,-32766,-32766,  454,  484,-32766,-32766,-32766,  519,-32766,
          511,-32766,  509,-32766,  502,-32766,-32766,-32766,-32766,  403,
        -32766,-32766,-32766,-32766,-32766,-32766,-32766,  618,-32766,  470,
        -32766,  793,  232,  453,  482,  271,  617,-32766,-32766,  507,
        -32766,  498,  469,  272,  407,  270,  552,  427, -247,  523,
        -32766,   34,  855,-32766,-32766,-32766,  203,-32766,   47,-32766,
          208,-32766,  344,   44,-32766,  803,-32766,-32766,-32766,-32766,
        -32766,  377,  378,-32766,-32766,  260,-32766,  261,-32766,  793,
          428,  616,  383,-32766,  820,  279,-32766,   33,  339,  552,
          340,  276,  278,-32766,  318,  410,-32766,-32766,-32766,  277,
        -32766,   32,-32766,  873,-32766,   31, -302,-32766,   30,   29,
          460,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  404,-32766,
          348,-32766,  793,  342,  274,  256,-32766,  306,  255,-32766,
           36,  254,  552,  241,  237,  236,-32766,  228,  227,-32766,
        -32766,-32766,  193,-32766,  132,-32766,  131,-32766,  123,   79,
        -32766,   78,   77,   76,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,   75,-32766,   48,-32766,  793,   37,   41, -313,-32766,
         -314,   35,-32766,  356,  421,  552,  472,  481,  518,-32766,
          779,  787,-32766,-32766,-32766,   19,-32766,   25,-32766,   28,
        -32766,  302,  480,-32766,  522,  759,  766,-32766,-32766,-32766,
          584,-32766,-32766,-32766,  804,-32766,  792,-32766,  793,  809,
          844,  788,-32766,  789,  790,-32766,  791,   22,  552,   24,
          763,  826,-32766,  762,  455,-32766,-32766,-32766,  457,-32766,
           23,-32766,   50,-32766,  593,  638,-32766,  630,  628,  637,
        -32766,-32766,-32766,  588,-32766,-32766,-32766,  632,-32766,  590,
        -32766,  793,  645,  197,  198,  581,  576,  596,-32766,   43,
          106,  107,  108,  109,  110,  111,  112,  113,  114,  115,
          116,   49,  603,  344,  375,  587,  585,  595,  589,  297,
          810,  811,  377,  378,  604,  536,  586,-32766,  812,  535,
          579,  382,  616,  383,  557,  344,  375,  726,  728,  127,
           81,  297,  526,  529,  377,  378,-32766,-32766,-32766,  534,
          275,  344,  375,  382,  616,  383,  538,  297,  560,  539,
          377,  378,-32766,  544,-32766,-32766,-32766,-32766,  128,  382,
          616,  383,  275,  450,  816,  817,  818,  813,  814,  328,
          559,  867,  869,  675,  857,  819,  815,  859,  275,-32767,
        -32767,-32767,-32767,   98,   99,  100,  101,  102,  344,  861,
          493,  344,  893,  894,  344,  260,  635,  377,  378,  636,
          377,  378,  892,  377,  378,  286,  428,  616,  383,  428,
          616,  383,  428,  616,  383,  780,  866,    0,  890,  856,
          858,    0,  344,  491,    0,  344,    0,    0,  344,    0,
            0,  377,  378,  341,  377,  378,  782,  377,  378,  613,
          428,  616,  383,  428,  616,  383,  428,  616,  383,    0,
            0,    0,    0,    0,    0,    0,  344,    0,    0,    0,
            0,    0,    0,    0,    0,  377,  378,  577,    0,    0,
          686,    0,    0,  608,  428,  616,  383
    );

    private static $yycheck = array(
            2,    3,    4,    5,    6,    8,    9,   10,   61,   11,
          103,  104,  105,  106,  107,  108,    8,    9,   10,   72,
           72,   24,    0,   26,   27,   28,   29,   30,   31,   32,
           33,   34,   24,   72,   26,   27,   28,   29,   30,   41,
           42,    8,    8,    9,   10,   47,  103,   49,   50,   51,
           52,   53,   54,   55,   56,   57,   58,   59,   24,   61,
           62,   63,   64,    7,  121,    7,   68,   69,   70,   71,
           72,    7,   74,    7,   76,   77,   78,   79,   25,   81,
          137,   83,   80,   85,    7,   86,   88,  139,  141,  141,
           92,   93,   94,   95,   95,   97,   98,   95,  100,    7,
          102,  103,  141,  104,  105,   25,  104,  105,  110,  111,
          112,   12,  113,  114,  115,  113,  114,  115,  120,  121,
          122,  123,  124,  125,  126,  127,    8,    9,   10,   25,
          132,  133,  134,  135,  136,    7,  138,  139,    7,  141,
          142,  143,   24,   72,   26,   27,   28,   29,   30,   31,
           32,   33,   34,   35,   36,   37,   38,   39,   40,   41,
           42,   43,   44,   45,   46,    7,   48,   70,   64,   60,
            8,    9,   10,   70,   70,   72,   73,   30,   74,   84,
           30,   77,   78,   79,    7,   81,   24,   83,   26,   85,
           95,  120,   88,  137,   70,  137,   92,   93,   94,  104,
          105,   97,   98,    7,  100,   70,  102,  103,  113,  114,
          115,   64,  141,    7,  110,  138,    7,   70,   96,  139,
           30,   74,   72,   72,   77,   78,   79,  103,   81,  137,
           83,  109,   85,   96,   60,   88,  139,    7,  141,   92,
           93,   94,  138,  139,   97,   98,  109,  100,  139,  102,
          103,   44,   45,   46,   64,   48,  132,  110,    7,  135,
           70,    7,  140,   30,   74,    7,  138,   77,   78,   79,
           70,   81,   25,   83,   75,   85,  141,  140,   88,   60,
          129,  130,   92,   93,   94,  138,  139,   97,   98,    7,
          100,  141,  102,  103,    8,    9,   10,   64,   60,   75,
          110,  103,  119,   70,    8,    9,   30,   74,   72,   75,
           77,   78,   79,  139,   81,  138,   83,    7,   85,  121,
          137,   88,   41,   42,   43,   92,   93,   94,  138,  139,
           97,   98,   72,  100,  138,  102,  103,   72,    7,   72,
           64,  141,  143,  110,  138,   25,   70,  138,   12,   30,
           74,   12,  128,   77,   78,   79,  118,   81,  139,   83,
           12,   85,  128,   12,   88,   75,   12,  137,   92,   93,
           94,  138,  139,   97,   98,   12,  100,  141,  102,  103,
          135,   65,   66,   64,  139,  138,  110,   12,  137,   70,
           12,  137,   30,   74,   65,   66,   77,   78,   79,   12,
           81,  141,   83,   12,   85,   12,  141,   88,  141,  138,
          139,   92,   93,   94,  138,  139,   97,   98,  128,  100,
           12,  102,  103,   90,   91,   25,   64,  138,  139,  110,
           48,   25,   70,   30,   25,   30,   74,   25,   25,   77,
           78,   79,   25,   81,   25,   83,   60,   85,   67,   70,
           88,   75,   72,   70,   92,   93,   94,  138,  139,   97,
           98,   72,  100,   75,  102,  103,   70,   70,   70,   64,
           75,   70,  110,   79,   70,   70,    8,    9,   10,   74,
           70,   70,   77,   78,   79,   70,   81,   95,   83,   87,
           85,   70,   24,   88,   26,   27,   28,   92,   93,   94,
          138,  139,   97,   98,   70,  100,   70,  102,  103,   70,
           70,   70,   64,   70,   70,  110,   70,   72,   70,    8,
            9,   10,   74,   72,   72,   77,   78,   79,   72,   81,
           72,   83,   72,   85,   72,   24,   88,   26,   27,   72,
           92,   93,   94,  138,  139,   97,   98,  114,  100,   99,
          102,  103,   87,   87,  101,  117,  114,  103,  110,   89,
           64,   89,  131,  116,  135,  116,   70,   71,  131,   82,
           74,  136,  140,   77,   78,   79,  119,   81,  119,   83,
          119,   85,   95,  119,   88,  137,  138,  139,   92,   93,
           94,  104,  105,   97,   98,  118,  100,  118,  102,  103,
          113,  114,  115,   64,  128,  131,  110,  136,  135,   70,
          135,  131,  131,   74,  131,  131,   77,   78,   79,  131,
           81,  136,   83,  137,   85,  136,  136,   88,  136,  136,
          136,   92,   93,   94,  138,  139,   97,   98,  136,  100,
          136,  102,  103,  136,  136,  136,   64,  137,  136,  110,
          136,  136,   70,  136,  136,  136,   74,  136,  136,   77,
           78,   79,  136,   81,  136,   83,  136,   85,  136,  136,
           88,  136,  136,  136,   92,   93,   94,  138,  139,   97,
           98,  136,  100,  136,  102,  103,  136,  136,  136,   64,
          136,  136,  110,  137,  137,   70,  137,  137,  137,   74,
          137,  137,   77,   78,   79,  137,   81,  137,   83,  137,
           85,  137,  137,   88,  137,  137,  137,   92,   93,   94,
          138,  139,   97,   98,  137,  100,  137,  102,  103,  137,
          137,  137,   64,  137,  137,  110,  137,  137,   70,  137,
          137,  137,   74,  137,  137,   77,   78,   79,  137,   81,
          137,   83,  138,   85,  138,  138,   88,  138,  138,  138,
           92,   93,   94,  138,  139,   97,   98,  138,  100,  138,
          102,  103,  138,   41,   42,  138,  138,  138,  110,   12,
           13,   14,   15,   16,   17,   18,   19,   20,   21,   22,
           23,  138,  138,   95,   96,  138,  138,  138,  138,  101,
           68,   69,  104,  105,  138,  138,  138,  139,   76,  138,
          138,  113,  114,  115,  138,   95,   96,   50,   51,  139,
          139,  101,  139,  139,  104,  105,    8,    9,   10,  139,
          132,   95,   96,  113,  114,  115,  139,  101,  140,  139,
          104,  105,   24,  139,   26,   27,   28,   29,  139,  113,
          114,  115,  132,  121,  122,  123,  124,  125,  126,  127,
          140,  140,  140,  140,  140,  133,  134,  140,  132,   35,
           36,   37,   38,   39,   40,   41,   42,   43,   95,  140,
          140,   95,  140,  140,   95,  118,  140,  104,  105,  140,
          104,  105,  140,  104,  105,  141,  113,  114,  115,  113,
          114,  115,  113,  114,  115,  142,  144,   -1,  144,  144,
          144,   -1,   95,  144,   -1,   95,   -1,   -1,   95,   -1,
           -1,  104,  105,  140,  104,  105,  140,  104,  105,  140,
          113,  114,  115,  113,  114,  115,  113,  114,  115,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   95,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,  104,  105,  140,   -1,   -1,
          140,   -1,   -1,  140,  113,  114,  115
    );

    private static $yybase = array(
            0,  698,  720,  736,  817,  783,    2,   -1,   95,  789,
          786,  487,  823,  820,  851,  851,  851,  851,  851,  409,
          406,  400,  400,  320,  400,   53,   -2,   -2,   -2,  405,
          147,  147,  147,  147,  147,  147,  147,  147,  582,  668,
          625,  448,  104,  190,  362,  233,  276,  319,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          496,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  247,  659,  658,  657,  654,  578,  579,
          587,  570,  568,  465,  574,  464,  752,  739,  419,  727,
          724,  723,  722,  575,  459,  769,  721,  740,  432,  572,
          577,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,   33,  296,  286,  286,  286,  286,  286,  286,  286,
          286,  286,  286,  286,  286,  286,  286,  286,   34,   34,
          162,  511,  468,  732,  732,  732,  732,  732,  732,  732,
          732,  732,  732,  732,  732,  732,  732,  732,  732,  732,
          732,  818,    8,   -3,   -3,   -3,   -3,   58,   92,  631,
          767,  834,  834,  834,  834,  137,  122,   71,   71,   71,
          -52,  -53,  150,  445,  281,  281,  260,  260,  260,  260,
          260,  260,  260,  260,  260,  260,  260,  260,  260,  260,
          260,  260,  260,  260,  -57,  -57,  -57,  224,  290,  199,
           97,   97,  198,  376,  388,  476,  395,  207,  207,  207,
          454,  454,  454,  454,  454,  245,  415,  200,  404,  135,
          681,  151,  151,  103,  681,  680,  709,  488,  474,  484,
          490,  490,  183,  472,  746,  470,  749,  473,  473,  -39,
          -39,  265,   80,  316,  329,  247,  289,  128,  437,  219,
          616,  434,  209,   77,  206,  238,  271,  174,  267,   56,
          607,  606,  603,  611,  743,  174,  109,  251,  234,  174,
          174,  486,  230,  600,  177,  196,  602,  254,  379,  379,
          379,  450,  403,  551,  403,  467,  403,  485,  440,  449,
          447,  258,  438,  403,  671,  667,  453,  450,  485,  403,
          403,   66,  462,  403,  403,   22,  545,  537,  413,  536,
          547,  535,  504,  533,  446,  441,  683,  442,  433,  519,
          518,  517,  502,  443,  522,  493,  429,  444,  480,  489,
          475,  382,  477,  492,  481,  477,  483,  479,  394,  666,
          378,  660,  402,  393,  557,  681,  763,  436,  421,  515,
          754,  532,  653,  282,  408,  512,  466,  439,  477,  451,
          477,  676,  684,  521,  477,  563,  411,  386,  494,  391,
          382,  382,  382,  471,  564,  158,  435,  552,  555,  589,
          766,  514,  550,  765,  435,  514,  592,  398,  556,   64,
          526,  437,  431,  410,  387,  639,  331,  375,  477,  764,
          559,  477,  477,  690,  614,  351,  560,  452,  476,  401,
          508,  477,  638,  593,  762,  331,  594,  596,  597,  599,
          637,  697,  507,  700,  363,  457,  604,  397,  392,  477,
          477,  742,  477,  554,  530,  634,  613,  629,  621,  460,
          458,  354,   99,  396,  528,  412,  381,  617,  389,  348,
          461,  561,  456,  339,  383,  477,  672,  619,  704,  336,
          509,  380,  417,  620,  510,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,    0,    0,    0,   -2,   -2,   -2,   -2,
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
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          124,  124,  124,  124,  124,  124,  124,  124,  124,  124,
          124,  124,  124,  124,  124,  124,  124,  124,  118,  118,
          118,  118,  118,  118,  767,  767,  767,    0,  207,  207,
          207,  207,  -93,  -93,  124,  124,  124,  124,  124,  124,
          -93,  207,  207,  124,  124,  124,  124,  124,  124,  124,
          124,  124,  124,  124,  124,  124,  124,  124,  124,  124,
          124,  124,  124,  124,  151,  151,  151,  265,  265,  124,
          151,  151,  151,  151,    0,    0,    0,  124,  124,  124,
          124,  124,  379,  236,  265,  236,  265,  265,    0,    0,
            0,  265,  265,  265,  174,  174,  174,  174,  174,  477,
          333,  333,  333,  333,  379,  379,    0,    0,    0,    0,
            0,    0,    0,    0,    0,  492,    0,  282,  515,    0,
            0,    0,    0,    0,    0,    0,    0,  310,  310,  477,
          131,  477,    0,    0,    0,    0,    0,    0,  131,    0,
          477,    0,    0,  477
    );

    private static $yydefault = array(
            3,32767,32767,    1,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  101,   94,  106,   93,  102,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,  328,
          117,  117,  117,  117,  117,  117,  117,  117,32767,32767,
        32767,32767,32767,32767,32767,  290,32767,32767,  160,  160,
          160,32767,  315,  315,  315,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,  333,32767,32767,32767,32767,
        32767,32767,32767,32767,  331,32767,32767,32767,32767,32767,
        32767,  217,  218,  220,  221,  159,32767,  316,  158,  332,
          330,  185,  187,  234,  186,  163,  168,  169,  170,  171,
          172,  173,  174,  175,  176,  177,  178,  162,  214,  213,
          183,  184,  188,  287,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  290,32767,32767,32767,32767,32767,
        32767,  190,  189,  205,  206,  203,  204,  295,  295,  295,
          295,  207,  208,  209,  210,  142,  142,  327,  327,  327,
        32767,32767,32767,  143,  197,  198,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  113,  113,  113,32767,32767,32767,
        32767,32767,  113,  258,32767,32767,  260,  192,  193,  191,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,  259,32767,32767,32767,32767,32767,  301,  301,  301,
          304,  305,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,  103,  105,32767,32767,32767,  283,  306,
        32767,32767,32767,32767,32767,  342,32767,  302,32767,32767,
        32767,32767,32767,32767,32767,  301,  317,  296,32767,  304,
          305,32767,  296,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,   64,  255,   64,  246,   64,  261,32767,   74,
           72,  289,   76,   64,   92,   92,  236,   55,  261,   64,
           64,  289,32767,   64,   64,32767,32767,32767,    5,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,    4,32767,32767,  303,
        32767,  201,  180,  247,32767,  182,  251,  252,32767,32767,
        32767,  296,   18,  131,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,  161,32767,32767,   20,32767,  127,32767,
           62,32767,32767,32767,  325,32767,32767,  281,32767,32767,
          194,  195,  196,  298,32767,  116,  351,  307,32767,32767,
        32767,  352,32767,32767,32767,32767,32767,32767,32767,  107,
        32767,  276,32767,32767,  129,32767,   75,32767,  337,32767,
        32767,  164,  120,32767,32767,32767,32767,32767,32767,32767,
        32767,   63,32767,32767,32767,   77,32767,32767,32767,32767,
        32767,32767,32767,32767,  109,  294,32767,32767,32767,  336,
          335,32767,  123,  308,32767,32767,32767,32767,32767,32767,
        32767,32767,  154,32767,32767,32767,32767,32767,32767,  111,
          292,32767,32767,32767,32767,  334,32767,32767,32767,  152,
        32767,32767,32767,32767,32767,   25,   25,    3,    3,  134,
           25,   99,   25,   25,  134,   92,   92,   25,   25,   25,
           25,   25,   25,   25,   25,   25,   25
    );

    private static $yygoto = array(
          145,  166,  166,  166,  166,  166,  166,  166,  166,  135,
          136,  166,  140,  148,  175,  169,  154,  166,  170,  165,
          165,  165,  165,  167,  167,  167,  161,  162,  163,  164,
          173,  746,  747,  391,  749,  769,  770,  771,  772,  773,
          774,  775,  777,  714,  137,  138,  139,  141,  142,  143,
          144,  146,  147,  171,  172,  174,  190,  191,  192,  211,
          212,  213,  214,  215,  216,  221,  222,  223,  224,  234,
          235,  267,  268,  269,  430,  431,  432,  176,  177,  178,
          179,  180,  181,  182,  183,  184,  185,  186,  149,  150,
          168,  151,  188,  152,  153,  155,  189,  156,  157,  158,
          187,  133,  159,  160,  451,  451,  451,  451,  451,  451,
          451,  451,  451,  451,  451,  451,  451,  451,  451,  451,
          451,  451,  439,  446,  473,  476,  477,  478,  479,  294,
          801,  464,  496,  801,  555,  555,  555,  801,  393,  393,
          393,  393,  393,  393,  466,  524,  316,  393,  393,  393,
          393,  794,  393,  393,  794,  393,  393,  393,  794,  393,
          393,  393,  393,  393,  393,  661,  661,  661,  402,  402,
          598,  310,  599,  661,  556,  556,  556,  443,  474,  266,
          259,  625,  625,  620,  626,  500,  452,  452,  452,  452,
          452,  452,  452,  452,  452,  452,  452,  452,  452,  452,
          452,  452,  452,  452,  758,    1,    2,  331,  358,  797,
          416,  796,  870,  692,  321,  800,  475,  360,  563,  671,
          394,  394,  394,  394,  394,  394,  540,  607,  835,  394,
          394,  394,  394,  396,  394,  394,  396,  394,  394,  394,
          396,  394,  394,  394,  394,  394,  394,  288,  288,  288,
          288,  288,  288,  712,  226,    0,  288,  288,  288,  288,
          289,  288,  288,  289,  288,  288,  288,  289,  288,  288,
          288,  288,  288,  288,    0,    0,    0,    0,    0,    0,
          325,  325,    0,    0,    0,  217,  217,  217,  217,  217,
          217,  217,  217,    0,    0,  219,  325,  325,  325,  325,
          317,  218,    0,    0,  290,  291,  330,    0,    0,  295,
          296,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,  329,  822,  822,  822,  822,  822,  822,  822,  822,
          822,  822,  822,  822,  822,  822,  822,  822,  822,  822,
          570,  679,  633,  824,  825,  569,  677,  634,  657,  840,
          510,  704,  702,  659,  838,  703,  700,    5,    0,    0,
            0,   14,    0,    6,    7,  554,  554,  554,    8,    9,
           10,   15,   16,   11,   17,   12,   18,   13,    0,    0,
            0,    0,    0,  644,  631,  629,  629,  627,  629,  531,
          398,  651,  647,  327,  327,  327,  327,  327,  327,  327,
          327,  850,    0,  401,  850,    0,    0,    0,  850,  332,
          311,    0,  409,    0,    0,    0,    0,    0,    0,  467,
            0,  865,  865,    0,    0,  252,  499,  885,  885,  514,
          521,    0,    0,    0,  888,  885,    0,  438,  442,  438,
          442,    0,    0,    0,    0,    0,    0,    0,    0,    0,
          888,  888,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,  424,  424,  424,  418,  461,  292,    0,    0,
            0,  420,  420,  392,  395,    0,  322,  324,    0,  458,
          462,  471,    0,  333,  489,  490,  492,  336,  515
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
           15,   15,   15,   15,   36,   36,   36,   36,   36,   36,
           36,   36,   36,   36,   36,   36,   36,   36,   36,   36,
           36,   36,   48,   48,   48,   48,   48,   48,   48,   44,
           74,   21,   21,   74,    7,    7,    7,   74,   36,   36,
           36,   36,   36,   36,   37,   37,    4,   36,   36,   36,
           36,   36,   36,   36,   36,   36,   36,   36,   36,   36,
           36,   36,   36,   36,   36,   36,   36,   36,    4,    4,
           30,   28,   30,   36,    8,    8,    8,   82,   82,   75,
           75,   36,   36,   36,   36,   36,   69,   69,   69,   69,
           69,   69,   69,   69,   69,   69,   69,   69,   69,   69,
           69,   69,   69,   69,   60,    2,    2,   59,   60,    4,
            4,    4,   84,   58,   29,   73,   43,   47,   11,   50,
           69,   69,   69,   69,   69,   69,   45,   35,   80,   69,
           69,   69,   69,   69,   69,   69,   69,   69,   69,   69,
           69,   69,   69,   69,   69,   69,   69,   71,   71,   71,
           71,   71,   71,   61,   41,   -1,   71,   71,   71,   71,
           71,   71,   71,   71,   71,   71,   71,   71,   71,   71,
           71,   71,   71,   71,   -1,   -1,   -1,   -1,   -1,   -1,
           71,   71,   -1,   -1,   -1,   25,   25,   25,   25,   25,
           25,   25,   25,   -1,   -1,   25,   71,   71,   71,   71,
           71,   25,   -1,   -1,   71,   71,   71,   -1,   -1,   44,
           44,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   71,   76,   76,   76,   76,   76,   76,   76,   76,
           76,   76,   76,   76,   76,   76,   76,   76,   76,   76,
           12,   12,   12,   12,   12,   12,   12,   12,   12,   12,
           12,   12,   12,   12,   12,   12,   12,   13,   -1,   -1,
           -1,   13,   -1,   13,   13,    6,    6,    6,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   -1,   -1,
           -1,   -1,   -1,    6,    6,    6,    6,    6,    6,    6,
            6,    6,    6,   24,   24,   24,   24,   24,   24,   24,
           24,   70,   -1,   24,   70,   -1,   -1,   -1,   70,   24,
           31,   -1,   31,   -1,   -1,   -1,   -1,   -1,   -1,   31,
           -1,   70,   70,   -1,   -1,   31,   31,   86,   86,   31,
           31,   -1,   -1,   -1,   86,   86,   -1,   70,   70,   70,
           70,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           86,   86,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   -1,   25,   25,   25,   25,   25,   25,   -1,   -1,
           -1,   25,   25,   25,   25,   -1,   25,   25,   -1,   25,
           25,   25,   -1,   25,   25,   25,   25,   25,   25
    );

    private static $yygbase = array(
            0,    0, -322,    0, -129,    0,  364,  133,  173,    0,
            0,  -80,  146, -169,    0,  -29,    0,    0,    0,    0,
            0,   82,    0,    0,  363,  255,    0,    0,  120,  -23,
          148,   66,    0,    0,    0, -130,  -89, -111,    0,    0,
            0, -280,    0,  -55, -226,  -79,    0,  -45,   91,    0,
          -81,    0,    0,    0,    0,    0,    0,    0,  -20,  -21,
          -39, -105,    0,    0,    0,    0,    0,    0,    0,   -7,
          161,   20,    0,  -46, -110,  -84,  129,    0,    0,    0,
         -133,    0,  124,    0,  -17,    0,  169,    0
    );

    private static $yygdefault = array(
        -32768,  365,    3,  549,  795,  386,  573,  574,  575,  312,
          307,  564,  485,    4,  571,  134,  303,  578,  304,  506,
          580,  412,  582,  583,  843,  220,  313,  314,  413,  320,
          597,  508,  319,  600,  357,  606,  308,  448,  387,  352,
          463,  225,  422,  456,  293,  543,  449,  353,  434,  435,
          672,  680,  362,  335,  334,  488,  684,  233,  691,  323,
          347,  713,  776,  778,  425,  406,  483,  337,  847,  388,
          389,  287,  397,  433,  846,  258,  831,  486,  829,  361,
          876,  309,  440,  326,  871,  351,  887,  459
    );

    private static $yylhs = array(
            0,    1,    2,    2,    4,    5,    5,    3,    3,    3,
            3,    3,    3,    3,    3,    3,    9,    9,   11,   11,
           11,   11,   10,   10,   13,   13,   14,   14,   14,   14,
            6,    6,    6,    6,    6,    6,    6,    6,    6,    6,
            6,    6,    6,    6,    6,    6,    6,    6,    6,    6,
            6,    6,    6,    6,    6,    6,    6,    6,    6,   34,
           34,   35,   29,   29,   31,   31,    7,    8,    8,   38,
           38,   38,   39,   39,   42,   42,   40,   40,   43,   43,
           22,   22,   30,   30,   33,   33,   32,   32,   23,   23,
           23,   23,   44,   44,   44,   45,   45,   20,   20,   16,
           16,   18,   18,   17,   17,   19,   19,   37,   37,   46,
           46,   46,   46,   47,   47,   47,   48,   48,   49,   49,
           49,   49,   49,   49,   26,   26,   50,   50,   50,   27,
           27,   27,   27,   41,   41,   51,   51,   51,   56,   56,
           52,   52,   55,   55,   57,   57,   58,   58,   58,   58,
           58,   58,   53,   53,   53,   53,   54,   54,   28,   28,
           21,   21,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   24,   24,   24,   24,
           24,   24,   24,   24,   24,   24,   66,   66,   67,   67,
           68,   68,   68,   68,   68,   68,   69,   69,   36,   36,
           36,   60,   60,   72,   72,   62,   62,   62,   65,   65,
           65,   61,   61,   76,   76,   76,   76,   76,   76,   76,
           76,   76,   76,   76,   76,   12,   12,   12,   12,   12,
           12,   63,   63,   63,   63,   63,   63,   77,   77,   80,
           80,   79,   79,   79,   79,   15,   15,   25,   25,   25,
           25,   70,   70,   74,   74,   74,   74,   81,   81,   81,
           81,   71,   71,   71,   71,   82,   82,   73,   73,   83,
           83,   83,   83,   59,   59,   84,   84,   84,   64,   64,
           85,   85,   85,   85,   85,   85,   85,   85,   75,   75,
           75,   75,   86,   86,   86,   86,   86,   86,   87,   87,
           87,   78,   78
    );

    private static $yylen = array(
            1,    1,    2,    0,    1,    1,    3,    1,    1,    1,
            4,    3,    5,    4,    3,    2,    3,    1,    1,    3,
            2,    4,    5,    4,    2,    0,    1,    1,    1,    4,
            3,    7,   10,    5,    7,    9,    5,    2,    3,    2,
            3,    2,    3,    3,    3,    3,    3,    1,    2,    5,
            7,    8,   10,    5,    1,    5,    3,    3,    2,    1,
            2,    8,    1,    3,    0,    1,    9,    7,    6,    1,
            2,    2,    0,    2,    0,    2,    0,    2,    1,    3,
            1,    4,    1,    4,    1,    4,    3,    5,    3,    4,
            4,    5,    0,    5,    4,    1,    1,    1,    4,    0,
            6,    0,    7,    0,    2,    0,    3,    1,    0,    3,
            5,    5,    7,    0,    1,    1,    1,    0,    1,    1,
            2,    3,    3,    4,    3,    1,    1,    2,    4,    3,
            5,    1,    3,    2,    0,    3,    2,    8,    1,    3,
            1,    1,    0,    1,    1,    2,    1,    1,    1,    1,
            1,    1,    3,    5,    1,    3,    5,    4,    3,    1,
            0,    1,    6,    3,    4,    6,    3,    2,    3,    3,
            3,    3,    3,    3,    3,    3,    3,    3,    3,    2,
            2,    2,    2,    3,    3,    3,    3,    3,    3,    3,
            3,    3,    3,    3,    3,    3,    3,    3,    3,    2,
            2,    2,    2,    3,    3,    3,    3,    3,    3,    3,
            3,    3,    3,    5,    4,    4,    4,    2,    2,    4,
            2,    2,    2,    2,    2,    2,    2,    2,    2,    2,
            2,    1,    4,    3,    2,    9,    0,    4,    4,    2,
            4,    6,    6,    6,    6,    4,    1,    1,    1,    3,
            2,    1,    1,    3,    1,    0,    2,    3,    0,    1,
            1,    0,    3,    1,    1,    1,    1,    1,    1,    1,
            1,    1,    1,    3,    2,    1,    1,    2,    2,    4,
            3,    1,    1,    1,    1,    3,    3,    0,    2,    0,
            1,    5,    3,    3,    1,    1,    1,    6,    3,    1,
            1,    1,    2,    1,    4,    4,    1,    3,    6,    4,
            4,    4,    4,    1,    4,    0,    1,    1,    1,    4,
            4,    1,    3,    3,    1,    1,    4,    0,    0,    2,
            5,    3,    3,    1,    6,    4,    4,    2,    2,    2,
            1,    2,    1,    4,    3,    3,    6,    3,    1,    1,
            1,    3,    3
    );

    protected function yyprintln($msg) {
        echo $msg, "\n";
    }

    protected function error($sym) {
        $errorCallback = $this->errorCallback;
        $errorCallback(
            'Parse error:'
            . ' Unexpected token ' . self::$yyterminals[$sym]
            . ' on line ' . $this->lex->getLine()
        );
    }

    /* Traditional Debug Mode */
    private function YYTRACE_NEWSTATE($state, $sym) {
        if ($this->yydebug) {
            $this->yyprintln('% State ' . $state . ', Lookahead '
                      . ($sym < 0 ? '--none--' : self::$yyterminals[$sym]));
        }
    }

    private function YYTRACE_READ($sym) {
        if ($this->yydebug)
            $this->yyprintln('% Reading ' . self::$yyterminals[$sym]);
    }

    private function YYTRACE_SHIFT($sym) {
        if ($this->yydebug)
            $this->yyprintln('% Shift ' . self::$yyterminals[$sym]);
    }

    private function YYTRACE_ACCEPT() {
        if ($this->yydebug)
            $this->yyprintln('% Accepted.');
    }

    private function YYTRACE_REDUCE($n) {
        if ($this->yydebug)
            $this->yyprintln('% Reduce by (' . $n . ') ' . self::$yyproduction[$n]);
    }

    private function YYTRACE_POP($state) {
        if ($this->yydebug)
            $this->yyprintln('% Recovering, uncovers state ' . $state);
    }

    private function YYTRACE_DISCARD($sym) {
        if ($this->yydebug)
            $this->yyprintln('% Discard ' . self::$yyterminals[$sym]);
    }

    /**
     * Parser entry point
     */
    public function yyparse($lex, $errorCallback) {
        $this->lex = $lex;
        $this->errorCallback  = $errorCallback;

        $this->yyastk = array();
        $yysstk = array();
        $this->yysp = 0;

        $yystate = 0;
        $yychar = -1;

        $yylval = null;
        $yysstk[$this->yysp] = 0;
        $yyerrflag = 0;

        for (;;) {
            $this->YYTRACE_NEWSTATE($yystate, $yychar);
            if (self::$yybase[$yystate] == 0) {
                $yyn = self::$yydefault[$yystate];
            } else {
                if ($yychar < 0) {
                    if (($yychar = $lex->yylex($yylval)) < 0)
                        $yychar = 0;
                    $yychar = $yychar < self::YYMAXLEX ?
                        self::$yytranslate[$yychar] : self::YYBADCH;
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

                        $yysstk[$this->yysp] = $yystate = $yyn;
                        $this->yyastk[$this->yysp] = $yylval;
                        $yychar = -1;

                        if ($yyerrflag > 0)
                            --$yyerrflag;
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
                    $this->{'yyn' . $yyn}();

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
                } else {
                    /* error */
                    switch ($yyerrflag) {
                    case 0:
                        $this->error($yychar);
                    case 1:
                    case 2:
                        $yyerrflag = 3;
                        /* Pop until error-expecting state uncovered */
                        while (!(($yyn = self::$yybase[$yystate] + self::YYINTERRTOK) >= 0
                                 && $yyn < self::YYLAST
                                 && self::$yycheck[$yyn] == self::YYINTERRTOK
                                 || ($yystate < self::YY2TBLSTATE
                                    && ($yyn = self::$yybase[$yystate + self::YYNLSTATES] + self::YYINTERRTOK) >= 0
                                    && $yyn < self::YYLAST
                                    && self::$yycheck[$yyn] == self::YYINTERRTOK))) {
                            if ($this->yysp <= 0) {
                                return false;
                            }
                            $yystate = $yysstk[--$this->yysp];
                            $this->YYTRACE_POP($yystate);
                        }
                        $yyn = self::$yyaction[$yyn];
                        $this->YYTRACE_SHIFT(self::YYINTERRTOK);
                        $yysstk[++$this->yysp] = $yystate = $yyn;
                        break;

                    case 3:
                        $this->YYTRACE_DISCARD($yychar);
                        if ($yychar == 0) {
                            return false;
                        }
                        $yychar = -1;
                        break;
                    }
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

    private function yyn1() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn2() {
         if (is_array($this->yyastk[$this->yysp-(2-2)])) { $this->yyval = array_merge($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); } else { $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; }; 
    }

    private function yyn3() {
         $this->yyval = array(); 
    }

    private function yyn4() {
         $this->yyval = new Node_Name(array('parts' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn5() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn6() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn7() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn8() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn9() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn10() {
         YYACCEPT; 
    }

    private function yyn11() {
         $this->yyval = new Node_Stmt_Namespace(array('ns' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn12() {
         $this->yyval = array(new Node_Stmt_Namespace(array('ns' => $this->yyastk[$this->yysp-(5-2)])), $this->yyastk[$this->yysp-(5-4)]); 
    }

    private function yyn13() {
         $this->yyval = array(new Node_Stmt_Namespace(array('ns' => null)), $this->yyastk[$this->yysp-(4-3)]); 
    }

    private function yyn14() {
         $this->yyval = new Node_Stmt_Use(array('uses' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn15() {
         $this->yyval = new Node_Stmt_Const(array('consts' => $this->yyastk[$this->yysp-(2-1)])); 
    }

    private function yyn16() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn17() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn18() {
         $this->yyval = new Node_Stmt_UseUse(array('ns' => $this->yyastk[$this->yysp-(1-1)], 'alias' => null)); 
    }

    private function yyn19() {
         $this->yyval = new Node_Stmt_UseUse(array('ns' => $this->yyastk[$this->yysp-(3-1)], 'alias' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn20() {
         $this->yyval = new Node_Stmt_UseUse(array('ns' => $this->yyastk[$this->yysp-(2-2)], 'alias' => null)); 
    }

    private function yyn21() {
         $this->yyval = new Node_Stmt_UseUse(array('ns' => $this->yyastk[$this->yysp-(4-2)], 'alias' => $this->yyastk[$this->yysp-(4-4)])); 
    }

    private function yyn22() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_ConstConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn23() {
         $this->yyval = array(new Node_Stmt_ConstConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)]))); 
    }

    private function yyn24() {
         if (is_array($this->yyastk[$this->yysp-(2-2)])) { $this->yyval = array_merge($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); } else { $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; }; 
    }

    private function yyn25() {
         $this->yyval = array(); 
    }

    private function yyn26() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn27() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn28() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn29() {
         error('__halt_compiler() can only be used from the outermost scope'); 
    }

    private function yyn30() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn31() {
         $this->yyval = new Node_Stmt_If(array('cond' => $this->yyastk[$this->yysp-(7-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-5)]) ? $this->yyastk[$this->yysp-(7-5)] : array($this->yyastk[$this->yysp-(7-5)]), 'elseifList' => $this->yyastk[$this->yysp-(7-6)], 'else' => $this->yyastk[$this->yysp-(7-7)])); 
    }

    private function yyn32() {
         $this->yyval = new Node_Stmt_If(array('cond' => $this->yyastk[$this->yysp-(10-3)], 'stmts' => $this->yyastk[$this->yysp-(10-6)], 'elseifList' => $this->yyastk[$this->yysp-(10-7)], 'else' => $this->yyastk[$this->yysp-(10-8)])); 
    }

    private function yyn33() {
         $this->yyval = new Node_Stmt_While(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)]))); 
    }

    private function yyn34() {
         $this->yyval = new Node_Stmt_Do(array('stmts' => is_array($this->yyastk[$this->yysp-(7-2)]) ? $this->yyastk[$this->yysp-(7-2)] : array($this->yyastk[$this->yysp-(7-2)]), 'cond' => $this->yyastk[$this->yysp-(7-5)])); 
    }

    private function yyn35() {
         $this->yyval = new Node_Stmt_For(array('init' => $this->yyastk[$this->yysp-(9-3)], 'cond' => $this->yyastk[$this->yysp-(9-5)], 'loop' => $this->yyastk[$this->yysp-(9-7)], 'stmts' => is_array($this->yyastk[$this->yysp-(9-9)]) ? $this->yyastk[$this->yysp-(9-9)] : array($this->yyastk[$this->yysp-(9-9)]))); 
    }

    private function yyn36() {
         $this->yyval = new Node_Stmt_Switch(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'caseList' => $this->yyastk[$this->yysp-(5-5)])); 
    }

    private function yyn37() {
         $this->yyval = new Node_Stmt_Break(array('num' => null)); 
    }

    private function yyn38() {
         $this->yyval = new Node_Stmt_Break(array('num' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn39() {
         $this->yyval = new Node_Stmt_Continue(array('num' => null)); 
    }

    private function yyn40() {
         $this->yyval = new Node_Stmt_Continue(array('num' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn41() {
         $this->yyval = new Node_Stmt_Return(array('expr' => null)); 
    }

    private function yyn42() {
         $this->yyval = new Node_Stmt_Return(array('expr' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn43() {
         $this->yyval = new Node_Stmt_Return(array('expr' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn44() {
         $this->yyval = new Node_Stmt_Global(array('vars' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn45() {
         $this->yyval = new Node_Stmt_Static(array('vars' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn46() {
         $this->yyval = new Node_Stmt_Echo(array('exprs' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn47() {
         $this->yyval = new Node_Stmt_InlineHTML(array('value' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn48() {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn49() {
         $this->yyval = new Node_Stmt_Unset(array('vars' => $this->yyastk[$this->yysp-(5-3)])); 
    }

    private function yyn50() {
         $this->yyval = new Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(7-3)], 'keyVar' => null, 'byRef' => false, 'valueVar' => $this->yyastk[$this->yysp-(7-5)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-7)]) ? $this->yyastk[$this->yysp-(7-7)] : array($this->yyastk[$this->yysp-(7-7)]))); 
    }

    private function yyn51() {
         $this->yyval = new Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(8-3)], 'keyVar' => null, 'byRef' => true, 'valueVar' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => is_array($this->yyastk[$this->yysp-(8-8)]) ? $this->yyastk[$this->yysp-(8-8)] : array($this->yyastk[$this->yysp-(8-8)]))); 
    }

    private function yyn52() {
         $this->yyval = new Node_Stmt_Foreach(array('expr' => $this->yyastk[$this->yysp-(10-3)], 'keyVar' => $this->yyastk[$this->yysp-(10-5)], 'byRef' => $this->yyastk[$this->yysp-(10-7)], 'valueVar' => $this->yyastk[$this->yysp-(10-8)], 'stmts' => is_array($this->yyastk[$this->yysp-(10-10)]) ? $this->yyastk[$this->yysp-(10-10)] : array($this->yyastk[$this->yysp-(10-10)]))); 
    }

    private function yyn53() {
         $this->yyval = new Node_Stmt_Declare(array('declares' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)]))); 
    }

    private function yyn54() {
         $this->yyval = new Node_Stmt_Noop(array()); 
    }

    private function yyn55() {
         $this->yyval = new Node_Stmt_TryCatch(array('stmts' => $this->yyastk[$this->yysp-(5-3)], 'catches' => $this->yyastk[$this->yysp-(5-5)])); 
    }

    private function yyn56() {
         $this->yyval = new Node_Stmt_Throw(array('expr' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn57() {
         $this->yyval = new Node_Stmt_Goto(array('name' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn58() {
         $this->yyval = new Node_Stmt_Label(array('name' => $this->yyastk[$this->yysp-(2-1)])); 
    }

    private function yyn59() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn60() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn61() {
         $this->yyval = new Node_Stmt_Catch(array('type' => $this->yyastk[$this->yysp-(8-3)], 'var' => substr($this->yyastk[$this->yysp-(8-4)], 1), 'stmts' => $this->yyastk[$this->yysp-(8-7)])); 
    }

    private function yyn62() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn63() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn64() {
         $this->yyval = false; 
    }

    private function yyn65() {
         $this->yyval = true; 
    }

    private function yyn66() {
         $this->yyval = new Node_Stmt_Func(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'name' => $this->yyastk[$this->yysp-(9-3)], 'params' => $this->yyastk[$this->yysp-(9-5)], 'stmts' => $this->yyastk[$this->yysp-(9-8)])); 
    }

    private function yyn67() {
         $this->yyval = new Node_Stmt_Class(array('type' => $this->yyastk[$this->yysp-(7-1)], 'name' => $this->yyastk[$this->yysp-(7-2)], 'extends' => $this->yyastk[$this->yysp-(7-3)], 'implements' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-6)])); 
    }

    private function yyn68() {
         $this->yyval = new Node_Stmt_Interface(array('name' => $this->yyastk[$this->yysp-(6-2)], 'extends' => $this->yyastk[$this->yysp-(6-3)], 'stmts' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn69() {
         $this->yyval = 0; 
    }

    private function yyn70() {
         $this->yyval = Node_Stmt_Class::MODIFIER_ABSTRACT; 
    }

    private function yyn71() {
         $this->yyval = Node_Stmt_Class::MODIFIER_FINAL; 
    }

    private function yyn72() {
         $this->yyval = null; 
    }

    private function yyn73() {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn74() {
         $this->yyval = array(); 
    }

    private function yyn75() {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn76() {
         $this->yyval = array(); 
    }

    private function yyn77() {
         $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn78() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn79() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn80() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn81() {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn82() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn83() {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn84() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn85() {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn86() {
         $this->yyval = array(new Node_Stmt_DeclareDeclare(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)]))); 
    }

    private function yyn87() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_DeclareDeclare(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn88() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn89() {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn90() {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn91() {
         $this->yyval = $this->yyastk[$this->yysp-(5-3)]; 
    }

    private function yyn92() {
         $this->yyval = array(); 
    }

    private function yyn93() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_Case(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn94() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_Stmt_Case(array('cond' => null, 'stmts' => $this->yyastk[$this->yysp-(4-4)])); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn95() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn96() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn97() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn98() {
         $this->yyval = $this->yyastk[$this->yysp-(4-2)]; 
    }

    private function yyn99() {
         $this->yyval = array();
    }

    private function yyn100() {
         $this->yyastk[$this->yysp-(6-1)][] = new Node_Stmt_ElseIf(array('cond' => $this->yyastk[$this->yysp-(6-4)], 'stmts' => is_array($this->yyastk[$this->yysp-(6-6)]) ? $this->yyastk[$this->yysp-(6-6)] : array($this->yyastk[$this->yysp-(6-6)]))); $this->yyval = $this->yyastk[$this->yysp-(6-1)]; 
    }

    private function yyn101() {
         $this->yyval = array(); 
    }

    private function yyn102() {
         $this->yyastk[$this->yysp-(7-1)][] = new Node_Stmt_ElseIf(array('cond' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-7)])); $this->yyval = $this->yyastk[$this->yysp-(7-1)]; 
    }

    private function yyn103() {
         $this->yyval = null; 
    }

    private function yyn104() {
         $this->yyval = new Node_Stmt_Else(array('stmts' => is_array($this->yyastk[$this->yysp-(2-2)]) ? $this->yyastk[$this->yysp-(2-2)] : array($this->yyastk[$this->yysp-(2-2)]))); 
    }

    private function yyn105() {
         $this->yyval = null; 
    }

    private function yyn106() {
         $this->yyval = new Node_Stmt_Else(array('stmts' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn107() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn108() {
         $this->yyval = array(); 
    }

    private function yyn109() {
         $this->yyval = array(new Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(3-2)], 'default' => null))); 
    }

    private function yyn110() {
         $this->yyval = array(new Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(5-1)], 'name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-2)], 'default' => $this->yyastk[$this->yysp-(5-5)]))); 
    }

    private function yyn111() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(5-3)], 'name' => substr($this->yyastk[$this->yysp-(5-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-4)], 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn112() {
         $this->yyastk[$this->yysp-(7-1)][] = new Node_Stmt_FuncParam(array('type' => $this->yyastk[$this->yysp-(7-3)], 'name' => substr($this->yyastk[$this->yysp-(7-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(7-4)], 'default' => $this->yyastk[$this->yysp-(7-7)])); $this->yyval = $this->yyastk[$this->yysp-(7-1)]; 
    }

    private function yyn113() {
         $this->yyval = null; 
    }

    private function yyn114() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn115() {
         $this->yyval = 'array'; 
    }

    private function yyn116() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn117() {
         $this->yyval = array(); 
    }

    private function yyn118() {
         $this->yyval = array(new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false))); 
    }

    private function yyn119() {
         $this->yyval = array(new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false))); 
    }

    private function yyn120() {
         $this->yyval = array(new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true))); 
    }

    private function yyn121() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn122() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn123() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_Expr_FuncCallArg(array('value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn124() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn125() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn126() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1))); 
    }

    private function yyn127() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn128() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn129() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn130() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn131() {
         $this->yyval = array(new Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null))); 
    }

    private function yyn132() {
         $this->yyval = array(new Node_Stmt_StaticVar(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)]))); 
    }

    private function yyn133() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn134() {
         $this->yyval = array(); 
    }

    private function yyn135() {
         $this->yyval = new Node_Stmt_Property(array('type' => $this->yyastk[$this->yysp-(3-1)], 'props' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn136() {
         $this->yyval = new Node_Stmt_ClassConst(array('consts' => $this->yyastk[$this->yysp-(2-1)])); 
    }

    private function yyn137() {
         $this->yyval = new Node_Stmt_ClassMethod(array('type' => $this->yyastk[$this->yysp-(8-1)], 'byRef' => $this->yyastk[$this->yysp-(8-3)], 'name' => $this->yyastk[$this->yysp-(8-4)], 'params' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => $this->yyastk[$this->yysp-(8-8)])); 
    }

    private function yyn138() {
         $this->yyval = null; 
    }

    private function yyn139() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn140() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn141() {
         $this->yyval = Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn142() {
         $this->yyval = Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn143() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn144() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn145() {
         Node_Stmt_Class::verifyModifier($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)] | $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn146() {
         $this->yyval = Node_Stmt_Class::MODIFIER_PUBLIC; 
    }

    private function yyn147() {
         $this->yyval = Node_Stmt_Class::MODIFIER_PROTECTED; 
    }

    private function yyn148() {
         $this->yyval = Node_Stmt_Class::MODIFIER_PRIVATE; 
    }

    private function yyn149() {
         $this->yyval = Node_Stmt_Class::MODIFIER_STATIC; 
    }

    private function yyn150() {
         $this->yyval = Node_Stmt_Class::MODIFIER_ABSTRACT; 
    }

    private function yyn151() {
         $this->yyval = Node_Stmt_Class::MODIFIER_FINAL; 
    }

    private function yyn152() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn153() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn154() {
         $this->yyval = array(new Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null))); 
    }

    private function yyn155() {
         $this->yyval = array(new Node_Stmt_PropertyProperty(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)]))); 
    }

    private function yyn156() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Stmt_ClassConstConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn157() {
         $this->yyval = array(new Node_Stmt_ClassConstConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)]))); 
    }

    private function yyn158() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn159() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn160() {
         $this->yyval = array(); 
    }

    private function yyn161() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn162() {
         $this->yyval = new Node_Expr_List(array('assignList' => $this->yyastk[$this->yysp-(6-3)], 'expr' => $this->yyastk[$this->yysp-(6-6)])); 
    }

    private function yyn163() {
         $this->yyval = new Node_Expr_Assign(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn164() {
         $this->yyval = new Node_Expr_AssignRef(array('var' => $this->yyastk[$this->yysp-(4-1)], 'refVar' => $this->yyastk[$this->yysp-(4-4)])); 
    }

    private function yyn165() {
         $this->yyval = new Node_Expr_Assign(array('var' => $this->yyastk[$this->yysp-(6-1)], 'expr' => new Node_Expr_New(array('class' => $this->yyastk[$this->yysp-(6-5)], 'args' => $this->yyastk[$this->yysp-(6-6)])))); 
    }

    private function yyn166() {
         $this->yyval = new Node_Expr_New(array('class' => $this->yyastk[$this->yysp-(3-2)], 'args' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn167() {
         $this->yyval = new Node_Expr_Clone(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn168() {
         $this->yyval = new Node_Expr_AssignPlus(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn169() {
         $this->yyval = new Node_Expr_AssignMinus(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn170() {
         $this->yyval = new Node_Expr_AssignMul(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn171() {
         $this->yyval = new Node_Expr_AssignDiv(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn172() {
         $this->yyval = new Node_Expr_AssignConcat(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn173() {
         $this->yyval = new Node_Expr_AssignMod(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn174() {
         $this->yyval = new Node_Expr_AssignBinAnd(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn175() {
         $this->yyval = new Node_Expr_AssignBinOr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn176() {
         $this->yyval = new Node_Expr_AssignBinXor(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn177() {
         $this->yyval = new Node_Expr_AssignShiftLeft(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn178() {
         $this->yyval = new Node_Expr_AssignShiftRight(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn179() {
         $this->yyval = new Node_Expr_PostInc(array('var' => $this->yyastk[$this->yysp-(2-1)])); 
    }

    private function yyn180() {
         $this->yyval = new Node_Expr_PreInc(array('var' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn181() {
         $this->yyval = new Node_Expr_PostDec(array('var' => $this->yyastk[$this->yysp-(2-1)])); 
    }

    private function yyn182() {
         $this->yyval = new Node_Expr_PreDec(array('var' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn183() {
         $this->yyval = new Node_Expr_BooleanOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn184() {
         $this->yyval = new Node_Expr_BooleanAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn185() {
         $this->yyval = new Node_Expr_LogicalOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn186() {
         $this->yyval = new Node_Expr_LogicalAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn187() {
         $this->yyval = new Node_Expr_LogicalXor(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn188() {
         $this->yyval = new Node_Expr_BinaryOr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn189() {
         $this->yyval = new Node_Expr_BinaryAnd(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn190() {
         $this->yyval = new Node_Expr_BinaryXor(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn191() {
         $this->yyval = new Node_Expr_Concat(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn192() {
         $this->yyval = new Node_Expr_Plus(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn193() {
         $this->yyval = new Node_Expr_Minus(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn194() {
         $this->yyval = new Node_Expr_Mul(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn195() {
         $this->yyval = new Node_Expr_Div(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn196() {
         $this->yyval = new Node_Expr_Mod(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn197() {
         $this->yyval = new Node_Expr_ShiftLeft(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn198() {
         $this->yyval = new Node_Expr_ShiftRight(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn199() {
         $this->yyval = new Node_Expr_UnaryPlus(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn200() {
         $this->yyval = new Node_Expr_UnaryMinus(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn201() {
         $this->yyval = new Node_Expr_BooleanNot(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn202() {
         $this->yyval = new Node_Expr_BinaryNot(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn203() {
         $this->yyval = new Node_Expr_Identical(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn204() {
         $this->yyval = new Node_Expr_NotIdentical(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn205() {
         $this->yyval = new Node_Expr_Equal(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn206() {
         $this->yyval = new Node_Expr_NotEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn207() {
         $this->yyval = new Node_Expr_Smaller(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn208() {
         $this->yyval = new Node_Expr_SmallerOrEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn209() {
         $this->yyval = new Node_Expr_Greater(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn210() {
         $this->yyval = new Node_Expr_GreaterOrEqual(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn211() {
         $this->yyval = new Node_Expr_InstanceOf(array('expr' => $this->yyastk[$this->yysp-(3-1)], 'class' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn212() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn213() {
         $this->yyval = new Node_Expr_Ternary(array('cond' => $this->yyastk[$this->yysp-(5-1)], 'if' => $this->yyastk[$this->yysp-(5-3)], 'else' => $this->yyastk[$this->yysp-(5-5)])); 
    }

    private function yyn214() {
         $this->yyval = new Node_Expr_Ternary(array('cond' => $this->yyastk[$this->yysp-(4-1)], 'if' => null, 'else' => $this->yyastk[$this->yysp-(4-4)])); 
    }

    private function yyn215() {
         $this->yyval = new Node_Expr_Isset(array('vars' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn216() {
         $this->yyval = new Node_Expr_Empty(array('var' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn217() {
         $this->yyval = new Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_Expr_Include::TYPE_INCLUDE)); 
    }

    private function yyn218() {
         $this->yyval = new Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_Expr_Include::TYPE_INCLUDE_ONCE)); 
    }

    private function yyn219() {
         $this->yyval = new Node_Expr_Eval(array('expr' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn220() {
         $this->yyval = new Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_Expr_Include::TYPE_REQUIRE)); 
    }

    private function yyn221() {
         $this->yyval = new Node_Expr_Include(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_Expr_Include::TYPE_REQUIRE_ONCE)); 
    }

    private function yyn222() {
         $this->yyval = new Node_Expr_IntCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn223() {
         $this->yyval = new Node_Expr_DoubleCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn224() {
         $this->yyval = new Node_Expr_StringCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn225() {
         $this->yyval = new Node_Expr_ArrayCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn226() {
         $this->yyval = new Node_Expr_ObjectCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn227() {
         $this->yyval = new Node_Expr_BoolCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn228() {
         $this->yyval = new Node_Expr_UnsetCast(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn229() {
         $this->yyval = new Node_Expr_Exit(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn230() {
         $this->yyval = new Node_Expr_ErrorSupress(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn231() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn232() {
         $this->yyval = new Node_Expr_Array(array('items' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn233() {
         $this->yyval = new Node_Expr_ShellExec(array('expr' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn234() {
         $this->yyval = new Node_Expr_Print(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn235() {
         $this->yyval = new Node_Expr_LambdaFunc(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'params' => $this->yyastk[$this->yysp-(9-4)], 'useVars' => $this->yyastk[$this->yysp-(9-6)], 'stmts' => $this->yyastk[$this->yysp-(9-8)])); 
    }

    private function yyn236() {
         $this->yyval = array(); 
    }

    private function yyn237() {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn238() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_Expr_LambdaFuncUse(array('var' => substr($this->yyastk[$this->yysp-(4-4)], 1), 'byRef' => $this->yyastk[$this->yysp-(4-3)])); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn239() {
         $this->yyval = array(new Node_Expr_LambdaFuncUse(array('var' => substr($this->yyastk[$this->yysp-(2-2)], 1), 'byRef' => $this->yyastk[$this->yysp-(2-1)]))); 
    }

    private function yyn240() {
         $this->yyval = new Node_Expr_FuncCall(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn241() {
         $this->yyval = new Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn242() {
         $this->yyval = new Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn243() {
         $this->yyval = new Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn244() {
         $this->yyval = new Node_Expr_StaticCall(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn245() {
         $this->yyval = new Node_Expr_FuncCall(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn246() {
         $this->yyval = 'static'; 
    }

    private function yyn247() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn248() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn249() {
         $this->yyastk[$this->yysp-(3-3)]->resolveType(Node_Name::RELATIVE); $this->yyval = $this->yyastk[$this->yysp-(3-3)]; 
    }

    private function yyn250() {
         $this->yyastk[$this->yysp-(2-2)]->resolveType(Node_Name::ABSOLUTE); $this->yyval = $this->yyastk[$this->yysp-(2-2)]; 
    }

    private function yyn251() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn252() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn253() {
         $this->yyval = new Node_Expr_PropertyFetch(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn254() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn255() {
         $this->yyval = null; 
    }

    private function yyn256() {
         $this->yyval = null; 
    }

    private function yyn257() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn258() {
         $this->yyval = null; 
    }

    private function yyn259() {
         $this->yyval = stripcslashes($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn260() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn261() {
         $this->yyval = array(); 
    }

    private function yyn262() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn263() {
         $this->yyval = new Node_Scalar_LNumber(array('value' => (int) $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn264() {
         $this->yyval = new Node_Scalar_DNumber(array('value' => (double) $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn265() {
         $this->yyval = new Node_Scalar_String(array('value' => str_replace(array('\\\'', '\\\\'), array('\'', '\\'), substr($this->yyastk[$this->yysp-(1-1)], 1, -1)))); 
    }

    private function yyn266() {
         $this->yyval = new Node_Scalar_LineConst(array()); 
    }

    private function yyn267() {
         $this->yyval = new Node_Scalar_FileConst(array()); 
    }

    private function yyn268() {
         $this->yyval = new Node_Scalar_DirConst(array()); 
    }

    private function yyn269() {
         $this->yyval = new Node_Scalar_ClassConst(array()); 
    }

    private function yyn270() {
         $this->yyval = new Node_Scalar_MethodConst(array()); 
    }

    private function yyn271() {
         $this->yyval = new Node_Scalar_FuncConst(array()); 
    }

    private function yyn272() {
         $this->yyval = new Node_Scalar_NSConst(array()); 
    }

    private function yyn273() {
         $this->yyval = new Node_Scalar_String(array('value' => stripcslashes($this->yyastk[$this->yysp-(3-2)]))); 
    }

    private function yyn274() {
         $this->yyval = new Node_Scalar_String(array('value' => '')); 
    }

    private function yyn275() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn276() {
         $this->yyval = new Node_Expr_ConstFetch(array('name' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn277() {
         $this->yyval = new Node_Expr_UnaryPlus(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn278() {
         $this->yyval = new Node_Expr_UnaryMinus(array('expr' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn279() {
         $this->yyval = new Node_Expr_Array(array('items' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn280() {
         $this->yyval = new Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn281() {
         $this->yyval = new Node_Scalar_String(array('value' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn282() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn283() {
         $this->yyval = new Node_Expr_ConstFetch(array('name' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn284() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn285() {
         $this->yyval = new Node_EncapsedString(array('parts' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn286() {
         $this->yyval = new Node_EncapsedString(array('parts' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn287() {
         $this->yyval = array(); 
    }

    private function yyn288() {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn289() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn290() {
        $this->yyval = $this->yyastk[$this->yysp];
    }

    private function yyn291() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn292() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn293() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false))); 
    }

    private function yyn294() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false))); 
    }

    private function yyn295() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn296() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn297() {
         $this->yyval = new Node_Expr_MethodCall(array('var' => $this->yyastk[$this->yysp-(6-1)], 'name' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)])); 
    }

    private function yyn298() {
         $this->yyval = new Node_Expr_PropertyFetch(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn299() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn300() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn301() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn302() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(2-2)])); 
    }

    private function yyn303() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn304() {
         $this->yyval = new Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(4-1)], 'name' => new Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-4)])))); 
    }

    private function yyn305() {
         $this->yyval = new Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(4-1)], 'name' => new Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-4)])))); 
    }

    private function yyn306() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn307() {
         $this->yyval = new Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1))); 
    }

    private function yyn308() {
         $this->yyval = new Node_Expr_StaticPropertyFetch(array('class' => $this->yyastk[$this->yysp-(6-1)], 'name' => new Node_Variable(array('name' => $this->yyastk[$this->yysp-(6-5)])))); 
    }

    private function yyn309() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn310() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn311() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn312() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn313() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1))); 
    }

    private function yyn314() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn315() {
         $this->yyval = null; 
    }

    private function yyn316() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn317() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn318() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn319() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn320() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn321() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn322() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn323() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn324() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn325() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)]; 
    }

    private function yyn326() {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)]; 
    }

    private function yyn327() {
         $this->yyval = null; 
    }

    private function yyn328() {
         $this->yyval = array(); 
    }

    private function yyn329() {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn330() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(5-1)]; 
    }

    private function yyn331() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)]; 
    }

    private function yyn332() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false))); 
    }

    private function yyn333() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false))); 
    }

    private function yyn334() {
         $this->yyastk[$this->yysp-(6-1)][] = new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(6-3)], 'value' => $this->yyastk[$this->yysp-(6-6)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(6-1)]; 
    }

    private function yyn335() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(4-1)]; 
    }

    private function yyn336() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => $this->yyastk[$this->yysp-(4-1)], 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true))); 
    }

    private function yyn337() {
         $this->yyval = array(new Node_Expr_ArrayItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true))); 
    }

    private function yyn338() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn339() {
         $this->yyastk[$this->yysp-(2-1)][] = stripcslashes($this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)]; 
    }

    private function yyn340() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]); 
    }

    private function yyn341() {
         $this->yyval = array(stripcslashes($this->yyastk[$this->yysp-(2-1)]), $this->yyastk[$this->yysp-(2-2)]); 
    }

    private function yyn342() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1))); 
    }

    private function yyn343() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(4-1)], 1))), 'dim' => $this->yyastk[$this->yysp-(4-3)])); 
    }

    private function yyn344() {
         $this->yyval = new Node_Expr_PropertyFetch(array('var' => new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1))), 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn345() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(3-2)])); 
    }

    private function yyn346() {
         $this->yyval = new Node_Expr_ArrayDimFetch(array('var' => new Node_Variable(array('name' => $this->yyastk[$this->yysp-(6-2)])), 'dim' => $this->yyastk[$this->yysp-(6-4)])); 
    }

    private function yyn347() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)]; 
    }

    private function yyn348() {
         $this->yyval = new Node_Scalar_String(array('value' => $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn349() {
         $this->yyval = new Node_Scalar_LNumber(array('value' => (int) $this->yyastk[$this->yysp-(1-1)])); 
    }

    private function yyn350() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1))); 
    }

    private function yyn351() {
         $this->yyval = new Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }

    private function yyn352() {
         $this->yyval = new Node_Expr_ClassConstFetch(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)])); 
    }
}
