<?php

class Parser
{
    const YYBADCH      = 145;
    const YYMAXLEX     = 380;
    const YYTERMS      = 145;
    const YYNONTERMS   = 87;
    const YYLAST       = 967;
    const YY2TBLSTATE  = 322;
    const YYGLAST      = 498;
    const YYSTATES     = 741;
    const YYNLSTATES   = 528;
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
        "function_call_parameter_list : non_empty_function_call_parameter_list",
        "function_call_parameter_list : /* empty */",
        "non_empty_function_call_parameter_list : expr_without_variable",
        "non_empty_function_call_parameter_list : variable",
        "non_empty_function_call_parameter_list : '&' variable",
        "non_empty_function_call_parameter_list : non_empty_function_call_parameter_list ',' expr_without_variable",
        "non_empty_function_call_parameter_list : non_empty_function_call_parameter_list ',' variable",
        "non_empty_function_call_parameter_list : non_empty_function_call_parameter_list ',' '&' variable",
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
        "function_call : name '(' function_call_parameter_list ')'",
        "function_call : class_name T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_parameter_list ')'",
        "function_call : class_name T_PAAMAYIM_NEKUDOTAYIM variable_without_objects '(' function_call_parameter_list ')'",
        "function_call : reference_variable T_PAAMAYIM_NEKUDOTAYIM T_STRING '(' function_call_parameter_list ')'",
        "function_call : reference_variable T_PAAMAYIM_NEKUDOTAYIM variable_without_objects '(' function_call_parameter_list ')'",
        "function_call : variable_without_objects '(' function_call_parameter_list ')'",
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
        "ctor_arguments : '(' function_call_parameter_list ')'",
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
        "variable : variable T_OBJECT_OPERATOR object_property '(' function_call_parameter_list ')'",
        "variable : variable T_OBJECT_OPERATOR object_property",
        "variable : base_variable",
        "variable : function_call",
        "variable_without_objects : reference_variable",
        "variable_without_objects : '$' reference_variable",
        "base_variable : variable_without_objects",
        "base_variable : class_name T_PAAMAYIM_NEKUDOTAYIM variable_without_objects",
        "base_variable : reference_variable T_PAAMAYIM_NEKUDOTAYIM variable_without_objects",
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
           54,   55,  351,   56,   57,-32766,-32766,-32766,  240,   58,
          677,  678,  679,  676,  675,  674,-32766,-32766,-32766,  836,
          836,-32766,    0,-32766,-32766,-32766,-32766,-32766,-32767,-32767,
        -32767,-32767,-32766,  844,-32766,-32766,-32766,-32766,-32766,   59,
           60,-32766,-32766,-32766,-32766,   61,-32766,   62,  231,  232,
           63,   64,   65,   66,   67,   68,   69,   70,-32766,  233,
           71,  328,  352,   45,  643, -122,  791,  792,  353,  809,
          836,   80,  575,  390,  793,   51,   26,  354,  125,  355,
         -108,  356,  476,  357,  286,  478,  358,  118,  273,  273,
           38,   39,  359,  331,  329,   40,  361,  329,   72, -121,
          287,  330,  120,  362,  363,  339,  362,  363,  364,  365,
          366,  187,  412,  597,  368,  412,  597,  368,  369,  370,
          797,  798,  799,  794,  795,  250,   81,   82,   83,  119,
          375,  800,  796,  323,   73,  290,  582,  506,   46,  273,
          256,  257,   42,  836,   84,   85,   86,   87,   88,   89,
           90,   91,   92,   93,   94,   95,   96,   97,   98,   99,
          100,  101,  102,  103,  104,  117,  236,  393,-32766,   52,
        -32766,-32766,-32766,  871,  533,  873,  872,  224,-32766,  498,
          241,-32766,-32766,-32766,  403,-32766,-32766,-32766,-32766,-32766,
          329,  407,-32766,  484,  533, -122,-32766,-32766,-32766,  362,
          363,-32766,-32766,  493,-32766,  836,-32766,  774,  412,  597,
          368,-32766,  273,  197,-32766,  542,  188,  533,  469,  340,
          237,-32766,  836,  303,-32766,-32766,-32766,  774,-32766, -121,
        -32766,  669,-32766,  469,  623,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,-32766,-32766,-32766,  669,-32766,  116,-32766,
          774,  102,  103,  104,-32766,  236,  375,-32766, -119,  323,
          533, -118,  596,  238,-32766,  349,  572,-32766,-32766,-32766,
          654,-32766,   53,-32766,  273,-32766,  429,  595,-32766,  276,
           79,  235,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  450,
        -32766,  273,-32766,  774,   99,  100,  101,-32766,  543,  862,
        -32766,  344,-32766,  533,  325,  258,  242,-32766,  508,  862,
        -32766,-32766,-32766,  255,-32766,  573,-32766,  266,-32766,   21,
          643,-32766,-32766,-32766,  664,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,  239,-32766,  663,-32766,  774,  410,  222,  223,
        -32766,  189,  802,-32766,  475,   27,  533,  624,  192,  243,
        -32766,  124,  193,-32766,-32766,-32766,  194,-32766,  802,-32766,
          195,-32766,  814,  198,-32766,  485,  486,  813,-32766,-32766,
        -32766,-32766,-32766,-32766,-32766,  199,-32766,  200,-32766,  774,
          126,  294,  202,-32766,  539,  509,-32766,  203, -119,  533,
          593, -118,  244,-32766,  522,  765,-32766,-32766,-32766,  526,
        -32766,  586,-32766,  123,-32766,  511,   20,-32766,  666,  527,
          513,-32766,-32766,-32766,-32766,-32766,-32766,-32766,  514,-32766,
          518,-32766,  774,  523,  122,  236,-32766,  334,  497,-32766,
         -140,  388,  533,  395,  401,  246,-32766,  335,  384,-32766,
        -32766,-32766,  385,-32766,  479,-32766,  274,-32766,  275,  398,
        -32766,  399,  413,  420,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,  424,-32766,  862,-32766,  774,  426,  427,  263,-32766,
          447,  482,-32766,  494,  501,  533,-32766,-32766,-32766,-32766,
          533,  534,-32766,-32766,-32766,  547,-32766,  867,-32766,  549,
        -32766,  808,-32766,-32766,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,-32766,-32766,-32766,  436,-32766,  466,-32766,  774,  483,
          490,  492,-32766,  500,  767,-32766,  766,  435,  533,  411,
          225,  348,-32766,  452,  488,-32766,-32766,-32766,  464,-32766,
          598,-32766,-32766,-32766,  599,  264,-32766,  801,  265, -247,
        -32766,-32766,-32766,-32766,-32766,-32766,-32766,  196,-32766,  253,
        -32766,  774,   49,  869,  254,-32766,  269,    0,-32766,  201,
           23,  533,-32766,-32766,-32766,-32766,   44,    0,-32766,-32766,
        -32766,   47,-32766,    0,-32766,    0,-32766,  271,-32766,-32766,
        -32766,-32766,  288,-32766,-32766,-32766,-32766,-32766,-32766,-32766,
          451,-32766,  270,-32766,  774,  272,  289,  325,  392,   80,
          761,-32766,  324,-32766,   50,   29,   30,   31,   32,  533,
           33,   34,  504,-32766,   35,   36,-32766,-32766,-32766,   37,
        -32766,   41,-32766,   48,-32766,  329,   74,-32766,  784,-32766,
        -32766,-32766,-32766,-32766,  362,  363,-32766,-32766,   75,-32766,
           76,-32766,  774,  412,  597,  368,-32766,   77,   78,-32766,
          121,  127,  533,  128,  186,  220,-32766,  221,  229,-32766,
        -32766,-32766,  230,-32766,  234,-32766,  295,-32766,  247,  248,
        -32766,  249,  267,  327,-32766,-32766,-32766,-32766,-32766,-32766,
        -32766,  333,-32766,  389,-32766,  774,  442,  516,  341,-32766,
          405,  507,-32766,  454,  463,  533,  499,  760,  768,-32766,
          773,  790,-32766,-32766,-32766,  825,-32766,  769,-32766,  770,
        -32766,  771,  772,-32766,  807,   22,   24,-32766,-32766,-32766,
          565,-32766,-32766,-32766,  744,-32766,   19,-32766,  774,   25,
           28,  291,-32766,  462,  503,-32766,  740,  747,  533,  785,
          439,  849,-32766,  437,  743,-32766,-32766,-32766,  517,-32766,
          585,-32766,  570,-32766,  538,  577,-32766,  557,  562,  626,
        -32766,-32766,-32766,  567,-32766,-32766,-32766,  613,-32766,  618,
        -32766,  774,  619,  190,  191,  560,  611,  609,-32766,   43,
          105,  106,  107,  108,  109,  110,  111,  112,  113,  114,
          115,  571,  576,  329,  360,  566,  568,  584,  574,  285,
          791,  792,  362,  363,  510,  515,  569,-32766,  793,  519,
          520,  367,  597,  368,  525,  329,  360,  707,  709,  870,
          616,  285,  617,  868,  362,  363,-32766,-32766,-32766,  837,
          268,  329,  360,  367,  597,  368,  835,  285,  541,  656,
          362,  363,-32766,  845,-32766,-32766,-32766,-32766,  843,  367,
          597,  368,  268,  432,  797,  798,  799,  794,  795,  315,
          540,  834,  866,  842,    0,  800,  796,  473,  268,-32767,
        -32767,-32767,-32767,   97,   98,   99,  100,  101,  329,    0,
            0,  329,    0,    0,  329,  253,    0,  362,  363,    0,
          362,  363,    0,  362,  363,    0,  412,  597,  368,  412,
          597,  368,  412,  597,  368,    0,    0,    0,    0,    0,
            0,    0,  329,    0,    0,  329,    0,    0,  329,    0,
            0,  362,  363,  326,  362,  363,  558,  362,  363,  594,
          412,  597,  368,  412,  597,  368,  412,  597,  368,    0,
            0,    0,    0,    0,    0,    0,  329,    0,    0,    0,
            0,    0,    0,    0,    0,  362,  363,  763,    0,    0,
          589,    0,    0,  667,  412,  597,  368
    );

    private static $yycheck = array(
            2,    3,    4,    5,    6,    8,    9,   10,   61,   11,
          103,  104,  105,  106,  107,  108,    8,    9,   10,   72,
           72,   24,    0,   26,   27,   28,   29,   30,   31,   32,
           33,   34,   24,   70,   26,   27,   28,   29,   30,   41,
           42,    8,    8,    9,   10,   47,  103,   49,   50,   51,
           52,   53,   54,   55,   56,   57,   58,   59,   24,   61,
           62,   63,   64,    7,  121,    7,   68,   69,   70,   71,
           72,  139,   74,  141,   76,   77,   78,   79,   12,   81,
          137,   83,   80,   85,    7,   86,   88,  139,  141,  141,
           92,   93,   94,   95,   95,   97,   98,   95,  100,    7,
          102,  103,  139,  104,  105,   25,  104,  105,  110,  111,
          112,   12,  113,  114,  115,  113,  114,  115,  120,  121,
          122,  123,  124,  125,  126,  127,    8,    9,   10,   25,
          132,  133,  134,  135,  136,    7,  138,  139,    7,  141,
          142,  143,   24,   72,   26,   27,   28,   29,   30,   31,
           32,   33,   34,   35,   36,   37,   38,   39,   40,   41,
           42,   43,   44,   45,   46,    7,   48,    7,   64,   60,
            8,    9,   10,   70,   70,   72,   73,   30,   74,   84,
           30,   77,   78,   79,    7,   81,   24,   83,   26,   85,
           95,  120,   88,    7,   70,  137,   92,   93,   94,  104,
          105,   97,   98,    7,  100,   72,  102,  103,  113,  114,
          115,   64,  141,    7,  110,  138,   12,   70,   96,  139,
           30,   74,   72,   72,   77,   78,   79,  103,   81,  137,
           83,  109,   85,   96,   25,   88,    8,    9,   10,   92,
           93,   94,  138,  139,   97,   98,  109,  100,  139,  102,
          103,   44,   45,   46,   64,   48,  132,  110,    7,  135,
           70,    7,  140,   30,   74,    7,  138,   77,   78,   79,
           72,   81,   60,   83,  141,   85,    7,  140,   88,   60,
          129,  130,   92,   93,   94,  138,  139,   97,   98,   75,
          100,  141,  102,  103,   41,   42,   43,   64,  138,   75,
          110,  119,  103,   70,  135,   75,   30,   74,  139,   75,
           77,   78,   79,    7,   81,  138,   83,    7,   85,  137,
          121,   88,    8,    9,  138,   92,   93,   94,  138,  139,
           97,   98,    7,  100,  138,  102,  103,  118,    7,  141,
           64,   12,  128,  110,   65,   66,   70,  138,   12,   30,
           74,  139,   12,   77,   78,   79,   12,   81,  128,   83,
           12,   85,  128,   12,   88,   65,   66,  143,   92,   93,
           94,  138,  139,   97,   98,   12,  100,   12,  102,  103,
           90,   91,   12,   64,  138,  139,  110,   12,  137,   70,
           30,  137,   30,   74,   25,  137,   77,   78,   79,   25,
           81,   25,   83,   25,   85,   25,  137,   88,  138,  139,
           25,   92,   93,   94,  138,  139,   97,   98,   25,  100,
           25,  102,  103,   25,   60,   48,   64,   70,   67,  110,
           72,   72,   70,   79,   87,   30,   74,   70,   70,   77,
           78,   79,   70,   81,   89,   83,   75,   85,   75,   70,
           88,   70,   70,   70,   92,   93,   94,  138,  139,   97,
           98,   70,  100,   75,  102,  103,   70,   70,  116,   64,
           70,   70,  110,   70,   70,   70,    8,    9,   10,   74,
           70,   70,   77,   78,   79,   70,   81,   70,   83,   70,
           85,   70,   24,   88,   26,   27,   28,   92,   93,   94,
          138,  139,   97,   98,   72,  100,   72,  102,  103,   72,
           72,   72,   64,   72,   72,  110,   72,   87,   70,   71,
           87,   95,   74,   99,   89,   77,   78,   79,  101,   81,
          114,   83,  103,   85,  114,  117,   88,  128,  116,  131,
           92,   93,   94,  138,  139,   97,   98,  119,  100,  118,
          102,  103,  138,  140,  118,   64,  131,   -1,  110,  119,
          137,   70,    8,    9,   10,   74,  119,   -1,   77,   78,
           79,  119,   81,   -1,   83,   -1,   85,  131,   24,   88,
           26,   27,  131,   92,   93,   94,  138,  139,   97,   98,
          131,  100,  131,  102,  103,  131,  131,  135,  135,  139,
          142,  110,  135,   64,  138,  136,  136,  136,  136,   70,
          136,  136,   82,   74,  136,  136,   77,   78,   79,  136,
           81,  136,   83,  136,   85,   95,  136,   88,  137,  138,
          139,   92,   93,   94,  104,  105,   97,   98,  136,  100,
          136,  102,  103,  113,  114,  115,   64,  136,  136,  110,
          136,  136,   70,  136,  136,  136,   74,  136,  136,   77,
           78,   79,  136,   81,  136,   83,  137,   85,  136,  136,
           88,  136,  136,  136,   92,   93,   94,  138,  139,   97,
           98,  136,  100,  136,  102,  103,  136,  138,  137,   64,
          137,  139,  110,  137,  137,   70,  137,  137,  137,   74,
          137,  137,   77,   78,   79,  137,   81,  137,   83,  137,
           85,  137,  137,   88,  137,  137,  137,   92,   93,   94,
          138,  139,   97,   98,  137,  100,  137,  102,  103,  137,
          137,  137,   64,  137,  137,  110,  137,  137,   70,  137,
          137,  137,   74,  137,  137,   77,   78,   79,  138,   81,
          138,   83,  138,   85,  138,  138,   88,  138,  138,  138,
           92,   93,   94,  138,  139,   97,   98,  138,  100,  138,
          102,  103,  138,   41,   42,  138,  138,  138,  110,   12,
           13,   14,   15,   16,   17,   18,   19,   20,   21,   22,
           23,  138,  138,   95,   96,  138,  138,  138,  138,  101,
           68,   69,  104,  105,  139,  139,  138,  139,   76,  139,
          139,  113,  114,  115,  139,   95,   96,   50,   51,  140,
          140,  101,  140,  140,  104,  105,    8,    9,   10,  140,
          132,   95,   96,  113,  114,  115,  140,  101,  140,  140,
          104,  105,   24,  140,   26,   27,   28,   29,  140,  113,
          114,  115,  132,  121,  122,  123,  124,  125,  126,  127,
          140,  144,  144,  144,   -1,  133,  134,  144,  132,   35,
           36,   37,   38,   39,   40,   41,   42,   43,   95,   -1,
           -1,   95,   -1,   -1,   95,  118,   -1,  104,  105,   -1,
          104,  105,   -1,  104,  105,   -1,  113,  114,  115,  113,
          114,  115,  113,  114,  115,   -1,   -1,   -1,   -1,   -1,
           -1,   -1,   95,   -1,   -1,   95,   -1,   -1,   95,   -1,
           -1,  104,  105,  140,  104,  105,  140,  104,  105,  140,
          113,  114,  115,  113,  114,  115,  113,  114,  115,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   95,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,  104,  105,  140,   -1,   -1,
          140,   -1,   -1,  140,  113,  114,  115
    );

    private static $yybase = array(
            0,  698,  720,  736,  786,  783,    2,   -1,   95,  789,
          817,  530,  820,  823,  851,  851,  851,  851,  851,  385,
          393,  395,  395,  398,  395,  380,   -2,   -2,   -2,  190,
          233,  233,  233,  233,  233,  233,  233,  233,  582,  625,
          668,  491,  104,  147,  276,  319,  362,  405,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  448,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  539,
          539,  539,  539,  539,  539,  539,  539,  539,  539,  209,
          654,  657,  658,  659,  599,  600,  602,  592,  589,  433,
          594,  447,  683,  689,  378,  696,  699,  703,  596,  452,
          723,  708,  593,  597,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,   33,  314,  228,  228,  228,  228,
          228,  228,  228,  228,  228,  228,  228,  228,  228,  228,
          228,   34,   34,  162,  554,  468,  732,  732,  732,  732,
          732,  732,  732,  732,  732,  732,  732,  732,  732,  732,
          732,  732,  732,  732,  818,    8,   -3,   -3,   -3,   -3,
          251,   58,  653,  767,  834,  834,  834,  834,  122,  137,
           71,   71,   71,  -52,  -53,  150,  358,  253,  253,  133,
          133,  133,  133,  133,  133,  133,  133,  133,  133,  133,
          133,  133,  133,  133,  133,  133,  133,  -57,  -57,  -57,
          230,  234,  224,  -37,  -37,  199,  371,  373,  409,  388,
          207,  207,  207,  429,  429,  429,  429,  429,  169,  383,
          391,  396,  397,  -68,  151,  151,  103,  461,  464,  465,
          182,  355,  680,  435,  682,  467,  467,  198,  133,  133,
          198,   80,  279,  300,  209,  270,  160,  408,  660,  381,
           77,  128,  177,  219,  246,  109,  269,  606,  607,  587,
          603,  679,  109,  212,  254,  214,  604,   92,  578,  186,
          196,  579,  258,  410,  410,  410,  424,  360,  485,  360,
          359,  360,  472,  379,  352,  422,   56,  418,  360,  549,
          610,  427,  424,  472,  360,  360,  206,  437,  360,  360,
           22,  490,  502,  376,  504,  487,  511,  545,  512,  368,
          372,  552,  416,  420,  522,  526,  528,  547,  357,  519,
          469,  463,  367,  425,  471,  462,  377,  431,  470,  446,
          431,  451,  436,  354,  612,   99,  614,  347,  204,  553,
          460,  458,  411,  382,  532,  514,  414,  158,  329,  533,
          430,  415,  431,  432,  431,  616,  665,  521,  431,  560,
          417,  364,  550,  336,  377,  377,  377,  474,  561,  131,
          475,  478,  563,  717,  479,  483,  475,  479,  564,  400,
          551,  306,  518,  408,  459,  419,  340,  617,  310,   66,
          431,  718,  556,  431,  431,  666,  466,  344,  557,  434,
          409,  421,  536,  431,  619,  568,  719,  310,  570,  572,
          574,  575,  620,  670,  537,  671,  348,  428,  577,  401,
          426,  431,  431,  413,  431,  515,  621,  423,  629,  631,
          438,  439,  351,  363,  403,  517,  369,  361,  634,  442,
          365,  440,  559,  441,  370,  404,  431,  637,  638,  675,
          375,  535,  444,  374,  639,  529,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,    0,    0,    0,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,   -2,
           -2,   -2,   -2,   -2,   -2,   -2,   -2,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  118,  118,  118,  118,  118,  118,
          118,  118,  118,  118,  124,  124,  124,  124,  124,  124,
          124,  124,  124,  124,  124,  124,  124,  124,  124,  124,
          124,  124,  118,  118,  118,  118,  118,  118,  767,  767,
          767,    0,  207,  207,  207,  207,  -93,  -93,  124,  124,
          124,  124,  124,  124,  -93,  207,  207,  124,  124,  124,
          124,  124,  124,  124,  124,  124,  124,  124,  124,  124,
          124,  124,  124,  124,  124,  124,  124,  124,  151,  151,
          151,  133,  133,  124,  151,  151,  151,  151,    0,    0,
            0,  124,  124,  124,  124,  124,  410,  133,  133,  133,
          133,  133,    0,    0,    0,  109,  109,  109,  431,  290,
          290,  290,  290,  410,  410,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,  470,  158,  532,    0,    0,
            0,    0,    0,    0,    0,  325,  325,  431,  331,  431,
            0,    0,    0,    0,  331,    0,  431,    0,    0,  431
    );

    private static $yydefault = array(
            3,32767,32767,    1,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  101,   94,  106,   93,  102,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,  323,
          117,  117,  117,  117,  117,  117,  117,  117,32767,32767,
        32767,32767,32767,32767,32767,  290,32767,32767,  160,  160,
          160,32767,  310,  310,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,  328,32767,32767,32767,32767,32767,32767,32767,  326,
        32767,32767,32767,32767,  217,  218,  220,  221,  159,32767,
          311,  158,  327,  325,  185,  187,  234,  186,  163,  168,
          169,  170,  171,  172,  173,  174,  175,  176,  177,  178,
          162,  214,  213,  183,  184,  188,  287,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,  290,32767,32767,
        32767,32767,32767,32767,  190,  189,  205,  206,  203,  204,
          295,  295,  295,  295,  207,  208,  209,  210,  142,  142,
          322,  322,  322,32767,32767,32767,  143,  197,  198,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,  113,  113,  113,
        32767,32767,32767,32767,32767,  113,  258,32767,32767,  260,
          192,  193,  191,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,  259,32767,32767,  301,  301,  301,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,32767,  103,  105,32767,32767,32767,  283,32767,32767,
        32767,32767,32767,  337,32767,  302,32767,32767,32767,32767,
        32767,32767,  301,  312,  296,32767,32767,  296,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,   64,  255,   64,
          246,   64,  261,32767,   74,   72,  289,   76,   64,   92,
           92,  236,   55,  261,   64,   64,  289,32767,   64,   64,
        32767,32767,32767,    5,32767,32767,32767,32767,32767,32767,
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
        32767,    4,32767,32767,  303,32767,  201,  180,  247,32767,
          182,  251,  252,32767,32767,32767,  296,   18,  131,32767,
        32767,32767,32767,32767,32767,32767,32767,  161,32767,32767,
           20,32767,  127,32767,   62,32767,32767,32767,  320,32767,
        32767,  281,32767,32767,  194,  195,  196,  298,32767,  116,
          346,  304,32767,32767,  347,  305,32767,32767,32767,32767,
        32767,  107,32767,  276,32767,32767,  129,32767,   75,32767,
          332,32767,32767,  164,  120,32767,32767,32767,32767,32767,
        32767,32767,32767,   63,32767,32767,32767,   77,32767,32767,
        32767,32767,32767,32767,32767,32767,  109,  294,32767,32767,
        32767,  331,  330,32767,  123,32767,32767,32767,32767,32767,
        32767,32767,32767,  154,32767,32767,32767,32767,32767,32767,
          111,  292,32767,32767,32767,32767,  329,32767,32767,32767,
          152,32767,32767,32767,32767,32767,   25,   25,    3,    3,
          134,   25,   99,   25,   25,  134,   92,   92,   25,   25,
           25,   25,   25,   25,   25,   25,   25,   25
    );

    private static $yygoto = array(
          141,  159,  159,  159,  159,  159,  159,  159,  159,  131,
          132,  159,  136,  144,  168,  162,  149,  159,  163,  158,
          158,  158,  158,  160,  160,  154,  155,  156,  157,  166,
          727,  728,  376,  730,  750,  751,  752,  753,  754,  755,
          756,  758,  695,  133,  134,  135,  137,  138,  139,  140,
          142,  143,  164,  165,  167,  183,  184,  185,  204,  205,
          206,  207,  208,  209,  214,  215,  216,  217,  227,  228,
          260,  261,  262,  414,  415,  416,  169,  170,  171,  172,
          173,  174,  175,  176,  177,  178,  179,  145,  161,  146,
          181,  147,  148,  150,  182,  151,  180,  129,  152,  153,
          433,  433,  433,  433,  433,  433,  433,  433,  433,  433,
          433,  433,  433,  433,  433,  433,  433,  433,  422,  428,
          455,  458,  459,  460,  461,  282,  782,  446,  477,  782,
          536,  536,  536,  782,  378,  378,  378,  378,  378,  378,
          537,  537,  537,  378,  378,  378,  378,  775,  378,  378,
          775,  378,  378,  378,  775,  378,  378,  378,  378,  378,
          378,  642,  642,  642,  739,  579,  298,  580,  343,  642,
          448,  505,  259,  252,    1,    2,  456,  606,  606,  601,
          607,  481,  434,  434,  434,  434,  434,  434,  434,  434,
          434,  434,  434,  434,  434,  434,  434,  434,  434,  434,
          861,  861,  316,  673,  846,  308,  781,  864,  861,  345,
          457,  544,  521,  652,  588,  816,  379,  379,  379,  379,
          379,  379,  693,  864,  864,  379,  379,  379,  379,  381,
          379,  379,  381,  379,  379,  379,  381,  379,  379,  379,
          379,  379,  379,  278,  278,  278,  278,  278,  278,  219,
            0,    0,  278,  278,  278,  278,  279,  278,  278,  279,
          278,  278,  278,  279,  278,  278,  278,  278,  278,  278,
            0,    0,    0,    0,    0,    0,  312,  312,    0,    0,
            0,  210,  210,  210,  210,  210,  210,  210,  210,    0,
            0,  212,  312,  312,  312,  312,  305,  211,    0,    0,
            0,  283,  284,    0,    0,    0,    0,    0,    0,    0,
            0,  312,  312,  803,  803,  803,  803,  803,  803,  803,
          803,  803,  803,  803,  803,  803,  803,  803,  803,  803,
          803,  551,  660,  614,  805,  806,  550,  658,  615,  638,
          821,  491,  685,  683,  640,  819,  684,  681,    5,    0,
            0,    0,   14,    0,    6,    7,  535,  535,  535,    8,
            9,   10,   15,   16,   11,   17,   12,   18,   13,    0,
            0,    0,    0,    0,  625,  612,  610,  610,  608,  610,
          512,  383,  632,  628,  831,    0,    0,  831,    0,  304,
            0,  831,  314,  314,  314,  314,  314,  314,  314,  314,
            0,    0,  386,    0,  841,  841,  387,  387,  317,  299,
            0,  394,    0,    0,    0,    0,    0,    0,  449,    0,
          421,  425,  421,  425,  245,  480,    0,    0,  495,  502,
            0,    0,    0,    0,    0,    0,    0,    0,    0,  832,
          833,    0,    0,    0,  778,  400,  777,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
            0,  408,  408,  408,  402,  443,  280,    0,    0,    0,
          404,  404,  377,  380,    0,  309,  311,    0,  440,  444,
          453,    0,  318,  471,  472,  474,  321,  496
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
           36,   36,   36,   36,   36,   36,   36,   36,   36,   36,
           36,   36,   36,   36,   36,   36,   36,   36,   48,   48,
           48,   48,   48,   48,   48,   44,   74,   21,   21,   74,
            7,    7,    7,   74,   36,   36,   36,   36,   36,   36,
            8,    8,    8,   36,   36,   36,   36,   36,   36,   36,
           36,   36,   36,   36,   36,   36,   36,   36,   36,   36,
           36,   36,   36,   36,   60,   30,   28,   30,   60,   36,
           37,   37,   75,   75,    2,    2,   81,   36,   36,   36,
           36,   36,   69,   69,   69,   69,   69,   69,   69,   69,
           69,   69,   69,   69,   69,   69,   69,   69,   69,   69,
           85,   85,   59,   58,   83,   29,   73,   85,   85,   47,
           43,   11,   45,   50,   35,   80,   69,   69,   69,   69,
           69,   69,   61,   85,   85,   69,   69,   69,   69,   69,
           69,   69,   69,   69,   69,   69,   69,   69,   69,   69,
           69,   69,   69,   71,   71,   71,   71,   71,   71,   41,
           -1,   -1,   71,   71,   71,   71,   71,   71,   71,   71,
           71,   71,   71,   71,   71,   71,   71,   71,   71,   71,
           -1,   -1,   -1,   -1,   -1,   -1,   71,   71,   -1,   -1,
           -1,   25,   25,   25,   25,   25,   25,   25,   25,   -1,
           -1,   25,   71,   71,   71,   71,   71,   25,   -1,   -1,
           -1,   44,   44,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   71,   71,   76,   76,   76,   76,   76,   76,   76,
           76,   76,   76,   76,   76,   76,   76,   76,   76,   76,
           76,   12,   12,   12,   12,   12,   12,   12,   12,   12,
           12,   12,   12,   12,   12,   12,   12,   12,   13,   -1,
           -1,   -1,   13,   -1,   13,   13,    6,    6,    6,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   -1,
           -1,   -1,   -1,   -1,    6,    6,    6,    6,    6,    6,
            6,    6,    6,    6,   70,   -1,   -1,   70,   -1,    4,
           -1,   70,   24,   24,   24,   24,   24,   24,   24,   24,
           -1,   -1,   24,   -1,   70,   70,    4,    4,   24,   31,
           -1,   31,   -1,   -1,   -1,   -1,   -1,   -1,   31,   -1,
           70,   70,   70,   70,   31,   31,   -1,   -1,   31,   31,
           -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   70,
           70,   -1,   -1,   -1,    4,    4,    4,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
           -1,   25,   25,   25,   25,   25,   25,   -1,   -1,   -1,
           25,   25,   25,   25,   -1,   25,   25,   -1,   25,   25,
           25,   -1,   25,   25,   25,   25,   25,   25
    );

    private static $yygbase = array(
            0,    0, -334,    0,  121,    0,  355,  129,  139,    0,
            0,  -75,  144, -159,    0,  -29,    0,    0,    0,    0,
            0,   78,    0,    0,  362,  251,    0,    0,  115,  -25,
          143,   80,    0,    0,    0, -128,  -86,  -78,    0,    0,
            0, -266,    0,  -54, -215,  -82,    0,  -46,   87,    0,
          -77,    0,    0,    0,    0,    0,    0,    0,  -23,  -19,
          -72, -121,    0,    0,    0,    0,    0,    0,    0,   -4,
          151,   23,    0,  -48, -107,  -84,  127,    0,    0,    0,
         -131,  123,    0,  -18,    0,  -51,    0
    );

    private static $yygdefault = array(
        -32768,  350,    3,  530,  776,  371,  554,  555,  556,  300,
          296,  545,  467,    4,  552,  130,  292,  559,  293,  487,
          561,  396,  563,  564,  824,  213,  301,  302,  397,  307,
          578,  489,  306,  581,  342,  587,  297,  430,  372,  337,
          445,  218,  406,  438,  281,  524,  431,  338,  418,  419,
          653,  661,  347,  320,  319,  470,  665,  226,  672,  310,
          332,  694,  757,  759,  409,  391,  465,  322,  828,  373,
          374,  277,  382,  417,  827,  251,  812,  468,  810,  346,
          852,  423,  313,  847,  336,  863,  441
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
           25,   70,   70,   74,   74,   74,   71,   71,   71,   71,
           81,   81,   73,   73,   82,   82,   82,   82,   59,   59,
           83,   83,   83,   64,   64,   84,   84,   84,   84,   84,
           84,   84,   84,   75,   75,   75,   75,   85,   85,   85,
           85,   85,   85,   86,   86,   86,   78,   78
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
            1,    1,    2,    1,    3,    3,    4,    4,    1,    4,
            0,    1,    1,    1,    4,    4,    1,    3,    3,    1,
            1,    4,    0,    0,    2,    5,    3,    3,    1,    6,
            4,    4,    2,    2,    2,    1,    2,    1,    4,    3,
            3,    6,    3,    1,    1,    1,    3,    3
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

    protected function accept() {
        $acceptCallback = $this->acceptCallback;
        $acceptCallback($this->yyval);
    }

    protected function yyaccept() {
        $this->yyaccept = 1;
    }

    protected function yyabort() {
        $this->yyaccept = 2;
    }

    /* Traditional Debug Mode */
    private function YYTRACE_NEWSTATE($state, $sym) {
        if ($this->yydebug) {
            $this->yyprintln("% State " . $state . ", Lookahead "
                      . ($sym < 0 ? "--none--" : self::$yyterminals[$sym]));
        }
    }

    private function YYTRACE_READ($sym) {
        if ($this->yydebug)
            $this->yyprintln("% Reading " . self::$yyterminals[$sym]);
    }

    private function YYTRACE_SHIFT($sym) {
        if ($this->yydebug)
            $this->yyprintln("% Shift " . self::$yyterminals[$sym]);
    }

    private function YYTRACE_ACCEPT() {
        if ($this->yydebug)
            $this->yyprintln("% Accepted.");
    }

    private function YYTRACE_REDUCE($n) {
        if ($this->yydebug)
            $this->yyprintln("% Reduce by (" . $n . ") " . self::$yyproduction[$n]);
    }

    private function YYTRACE_POP($state) {
        if ($this->yydebug)
            $this->yyprintln("% Recovering, uncovers state " . $state);
    }

    private function YYTRACE_DISCARD($sym) {
        if ($this->yydebug)
            $this->yyprintln("% Discard " . self::$yyterminals[$sym]);
    }

    /**
     * Parser entry point
     */
    public function yyparse($lex, $acceptCallback, $errorCallback) {
        $this->lex = $lex;
        $this->acceptCallback = $acceptCallback;
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
                        $this->yysp++;

                        $yysstk[$this->yysp] = $yystate = $yyn;
                        $this->yyastk[$this->yysp] = $yylval;
                        $yychar = -1;

                        if ($yyerrflag > 0)
                            $yyerrflag--;
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
                    $this->accept();
                    return $this->yyaccept - 1;
                } elseif ($yyn != self::YYUNEXPECTED) {
                    /* reduce */
                    $yyl = self::$yylen[$yyn];
                    $n = $this->yysp-$yyl+1;
                    $yyval = isset($this->yyastk[$n]) ? $this->yyastk[$n] : null;
                    $this->YYTRACE_REDUCE($yyn);
                    $this->{'yyn' . $yyn}();
                    if ($this->yyaccept) {
                        $yyn = self::YYNLSTATES;
                    } else {
                        /* Goto - shift nonterminal */
                        $this->yysp -= $yyl;
                        $yyn = self::$yylhs[$yyn];
                        if (($yyp = self::$yygbase[$yyn] + $yysstk[$this->yysp]) >= 0
                             && $yyp < self::YYGLAST
                             && self::$yygcheck[$yyp] == $yyn) {
                            $yystate = self::$yygoto[$yyp];
                        } else {
                            $yystate = self::$yygdefault[$yyn];
                        }

                        $this->yysp++;

                        $yysstk[$this->yysp] = $yystate;
                        $this->yyastk[$this->yysp] = $this->yyval;
                    }
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
                                return 1;
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
                            return 1;
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
    private function yyn0() {}

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
         $this->yyval = new Node_NamespaceStmt(array('ns' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn12() {
         $this->yyval = array(new Node_NamespaceStmt(array('ns' => $this->yyastk[$this->yysp-(5-2)])), $this->yyastk[$this->yysp-(5-4)]);
    }

    private function yyn13() {
         $this->yyval = array(new Node_NamespaceStmt(array('ns' => null)), $this->yyastk[$this->yysp-(4-3)]);
    }

    private function yyn14() {
         $this->yyval = new Node_UseStmt(array('uses' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn15() {
         $this->yyval = new Node_ConstStmt(array('consts' => $this->yyastk[$this->yysp-(2-1)]));
    }

    private function yyn16() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn17() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]);
    }

    private function yyn18() {
         $this->yyval = new Node_UseStmtUse(array('ns' => $this->yyastk[$this->yysp-(1-1)], 'alias' => null));
    }

    private function yyn19() {
         $this->yyval = new Node_UseStmtUse(array('ns' => $this->yyastk[$this->yysp-(3-1)], 'alias' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn20() {
         $this->yyval = new Node_UseStmtUse(array('ns' => $this->yyastk[$this->yysp-(2-2)], 'alias' => null));
    }

    private function yyn21() {
         $this->yyval = new Node_UseStmtUse(array('ns' => $this->yyastk[$this->yysp-(4-2)], 'alias' => $this->yyastk[$this->yysp-(4-4)]));
    }

    private function yyn22() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_ConstStmtConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn23() {
         $this->yyval = array(new Node_ConstStmtConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)])));
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
         $this->yyval = new Node_IfStmt(array('cond' => $this->yyastk[$this->yysp-(7-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-5)]) ? $this->yyastk[$this->yysp-(7-5)] : array($this->yyastk[$this->yysp-(7-5)]), 'elseifList' => $this->yyastk[$this->yysp-(7-6)], 'else' => $this->yyastk[$this->yysp-(7-7)]));
    }

    private function yyn32() {
         $this->yyval = new Node_IfStmt(array('cond' => $this->yyastk[$this->yysp-(10-3)], 'stmts' => $this->yyastk[$this->yysp-(10-6)], 'elseifList' => $this->yyastk[$this->yysp-(10-7)], 'else' => $this->yyastk[$this->yysp-(10-8)]));
    }

    private function yyn33() {
         $this->yyval = new Node_WhileStmt(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)])));
    }

    private function yyn34() {
         $this->yyval = new Node_DoStmt(array('stmts' => is_array($this->yyastk[$this->yysp-(7-2)]) ? $this->yyastk[$this->yysp-(7-2)] : array($this->yyastk[$this->yysp-(7-2)]), 'cond' => $this->yyastk[$this->yysp-(7-5)]));
    }

    private function yyn35() {
         $this->yyval = new Node_ForStmt(array('init' => $this->yyastk[$this->yysp-(9-3)], 'cond' => $this->yyastk[$this->yysp-(9-5)], 'loop' => $this->yyastk[$this->yysp-(9-7)], 'stmts' => is_array($this->yyastk[$this->yysp-(9-9)]) ? $this->yyastk[$this->yysp-(9-9)] : array($this->yyastk[$this->yysp-(9-9)])));
    }

    private function yyn36() {
         $this->yyval = new Node_SwitchStmt(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'caseList' => $this->yyastk[$this->yysp-(5-5)]));
    }

    private function yyn37() {
         $this->yyval = new Node_BreakStmt(array('num' => null));
    }

    private function yyn38() {
         $this->yyval = new Node_BreakStmt(array('num' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn39() {
         $this->yyval = new Node_ContinueStmt(array('num' => null));
    }

    private function yyn40() {
         $this->yyval = new Node_ContinueStmt(array('num' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn41() {
         $this->yyval = new Node_ReturnStmt(array('expr' => null));
    }

    private function yyn42() {
         $this->yyval = new Node_ReturnStmt(array('expr' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn43() {
         $this->yyval = new Node_ReturnStmt(array('expr' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn44() {
         $this->yyval = new Node_GlobalStmt(array('vars' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn45() {
         $this->yyval = new Node_StaticStmt(array('vars' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn46() {
         $this->yyval = new Node_EchoStmt(array('exprs' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn47() {
         $this->yyval = new Node_InlineHTMLStmt(array('value' => $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn48() {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn49() {
         $this->yyval = new Node_UnsetStmt(array('vars' => $this->yyastk[$this->yysp-(5-3)]));
    }

    private function yyn50() {
         $this->yyval = new Node_ForeachStmt(array('expr' => $this->yyastk[$this->yysp-(7-3)], 'keyVar' => null, 'byRef' => false, 'valueVar' => $this->yyastk[$this->yysp-(7-5)], 'stmts' => is_array($this->yyastk[$this->yysp-(7-7)]) ? $this->yyastk[$this->yysp-(7-7)] : array($this->yyastk[$this->yysp-(7-7)])));
    }

    private function yyn51() {
         $this->yyval = new Node_ForeachStmt(array('expr' => $this->yyastk[$this->yysp-(8-3)], 'keyVar' => null, 'byRef' => true, 'valueVar' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => is_array($this->yyastk[$this->yysp-(8-8)]) ? $this->yyastk[$this->yysp-(8-8)] : array($this->yyastk[$this->yysp-(8-8)])));
    }

    private function yyn52() {
         $this->yyval = new Node_ForeachStmt(array('expr' => $this->yyastk[$this->yysp-(10-3)], 'keyVar' => $this->yyastk[$this->yysp-(10-5)], 'byRef' => $this->yyastk[$this->yysp-(10-7)], 'valueVar' => $this->yyastk[$this->yysp-(10-8)], 'stmts' => is_array($this->yyastk[$this->yysp-(10-10)]) ? $this->yyastk[$this->yysp-(10-10)] : array($this->yyastk[$this->yysp-(10-10)])));
    }

    private function yyn53() {
         $this->yyval = new Node_DeclareStmt(array('declares' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => is_array($this->yyastk[$this->yysp-(5-5)]) ? $this->yyastk[$this->yysp-(5-5)] : array($this->yyastk[$this->yysp-(5-5)])));
    }

    private function yyn54() {
         $this->yyval = new Node_NoopStmt(array());
    }

    private function yyn55() {
         $this->yyval = new Node_TryCatchStmt(array('stmts' => $this->yyastk[$this->yysp-(5-3)], 'catches' => $this->yyastk[$this->yysp-(5-5)]));
    }

    private function yyn56() {
         $this->yyval = new Node_ThrowStmt(array('expr' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn57() {
         $this->yyval = new Node_GotoStmt(array('name' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn58() {
         $this->yyval = new Node_LabelStmt(array('name' => $this->yyastk[$this->yysp-(2-1)]));
    }

    private function yyn59() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]);
    }

    private function yyn60() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn61() {
         $this->yyval = new Node_CatchStmt(array('type' => $this->yyastk[$this->yysp-(8-3)], 'var' => substr($this->yyastk[$this->yysp-(8-4)], 1), 'stmts' => $this->yyastk[$this->yysp-(8-7)]));
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
         $this->yyval = new Node_FuncStmt(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'name' => $this->yyastk[$this->yysp-(9-3)], 'params' => $this->yyastk[$this->yysp-(9-5)], 'stmts' => $this->yyastk[$this->yysp-(9-8)]));
    }

    private function yyn67() {
         $this->yyval = new Node_ClassStmt(array('type' => $this->yyastk[$this->yysp-(7-1)], 'name' => $this->yyastk[$this->yysp-(7-2)], 'extends' => $this->yyastk[$this->yysp-(7-3)], 'implements' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-6)]));
    }

    private function yyn68() {
         $this->yyval = new Node_InterfaceStmt(array('name' => $this->yyastk[$this->yysp-(6-2)], 'extends' => $this->yyastk[$this->yysp-(6-3)], 'stmts' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn69() {
         $this->yyval = 0;
    }

    private function yyn70() {
         $this->yyval = Node_ClassStmt::MODIFIER_ABSTRACT;
    }

    private function yyn71() {
         $this->yyval = Node_ClassStmt::MODIFIER_FINAL;
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
         $this->yyval = array(new Node_DeclareStmtDeclare(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)])));
    }

    private function yyn87() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_DeclareStmtDeclare(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
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
         $this->yyastk[$this->yysp-(5-1)][] = new Node_CaseStmt(array('cond' => $this->yyastk[$this->yysp-(5-3)], 'stmts' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn94() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_CaseStmt(array('cond' => null, 'stmts' => $this->yyastk[$this->yysp-(4-4)])); $this->yyval = $this->yyastk[$this->yysp-(4-1)];
    }
    private function yyn95() {}
    private function yyn96() {}

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
         $this->yyastk[$this->yysp-(6-1)][] = new Node_ElseIfStmt(array('cond' => $this->yyastk[$this->yysp-(6-4)], 'stmts' => is_array($this->yyastk[$this->yysp-(6-6)]) ? $this->yyastk[$this->yysp-(6-6)] : array($this->yyastk[$this->yysp-(6-6)]))); $this->yyval = $this->yyastk[$this->yysp-(6-1)];
    }

    private function yyn101() {
         $this->yyval = array();
    }

    private function yyn102() {
         $this->yyastk[$this->yysp-(7-1)][] = new Node_ElseIfStmt(array('cond' => $this->yyastk[$this->yysp-(7-4)], 'stmts' => $this->yyastk[$this->yysp-(7-7)])); $this->yyval = $this->yyastk[$this->yysp-(7-1)];
    }

    private function yyn103() {
         $this->yyval = null;
    }

    private function yyn104() {
         $this->yyval = new Node_ElseStmt(array('stmts' => is_array($this->yyastk[$this->yysp-(2-2)]) ? $this->yyastk[$this->yysp-(2-2)] : array($this->yyastk[$this->yysp-(2-2)])));
    }

    private function yyn105() {
         $this->yyval = null;
    }

    private function yyn106() {
         $this->yyval = new Node_ElseStmt(array('stmts' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn107() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn108() {
         $this->yyval = array();
    }

    private function yyn109() {
         $this->yyval = array(new Node_FuncStmtParam(array('type' => $this->yyastk[$this->yysp-(3-1)], 'name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(3-2)], 'default' => null)));
    }

    private function yyn110() {
         $this->yyval = array(new Node_FuncStmtParam(array('type' => $this->yyastk[$this->yysp-(5-1)], 'name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-2)], 'default' => $this->yyastk[$this->yysp-(5-5)])));
    }

    private function yyn111() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_FuncStmtParam(array('type' => $this->yyastk[$this->yysp-(5-3)], 'name' => substr($this->yyastk[$this->yysp-(5-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(5-4)], 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn112() {
         $this->yyastk[$this->yysp-(7-1)][] = new Node_FuncStmtParam(array('type' => $this->yyastk[$this->yysp-(7-3)], 'name' => substr($this->yyastk[$this->yysp-(7-5)], 1), 'byRef' => $this->yyastk[$this->yysp-(7-4)], 'default' => $this->yyastk[$this->yysp-(7-7)])); $this->yyval = $this->yyastk[$this->yysp-(7-1)];
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
         $this->yyval = array(new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false)));
    }

    private function yyn119() {
         $this->yyval = array(new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false)));
    }

    private function yyn120() {
         $this->yyval = array(new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true)));
    }

    private function yyn121() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn122() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn123() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_FuncCallStmtParam(array('value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(4-1)];
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
         $this->yyastk[$this->yysp-(3-1)][] = new Node_StaticStmtVar(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn130() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_StaticStmtVar(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn131() {
         $this->yyval = array(new Node_StaticStmtVar(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null)));
    }

    private function yyn132() {
         $this->yyval = array(new Node_StaticStmtVar(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)])));
    }

    private function yyn133() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn134() {
         $this->yyval = array();
    }

    private function yyn135() {
         $this->yyval = new Node_PropertyStmt(array('type' => $this->yyastk[$this->yysp-(3-1)], 'props' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn136() {
         $this->yyval = new Node_ClassConstStmt(array('consts' => $this->yyastk[$this->yysp-(2-1)]));
    }

    private function yyn137() {
         $this->yyval = new Node_ClassMethodStmt(array('type' => $this->yyastk[$this->yysp-(8-1)], 'byRef' => $this->yyastk[$this->yysp-(8-3)], 'name' => $this->yyastk[$this->yysp-(8-4)], 'params' => $this->yyastk[$this->yysp-(8-6)], 'stmts' => $this->yyastk[$this->yysp-(8-8)]));
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
         $this->yyval = Node_ClassStmt::MODIFIER_PUBLIC;
    }

    private function yyn142() {
         $this->yyval = Node_ClassStmt::MODIFIER_PUBLIC;
    }

    private function yyn143() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn144() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn145() {
         Node_ClassStmt::verifyModifier($this->yyastk[$this->yysp-(2-1)], $this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)] | $this->yyastk[$this->yysp-(2-2)];
    }

    private function yyn146() {
         $this->yyval = Node_ClassStmt::MODIFIER_PUBLIC;
    }

    private function yyn147() {
         $this->yyval = Node_ClassStmt::MODIFIER_PROTECTED;
    }

    private function yyn148() {
         $this->yyval = Node_ClassStmt::MODIFIER_PRIVATE;
    }

    private function yyn149() {
         $this->yyval = Node_ClassStmt::MODIFIER_STATIC;
    }

    private function yyn150() {
         $this->yyval = Node_ClassStmt::MODIFIER_ABSTRACT;
    }

    private function yyn151() {
         $this->yyval = Node_ClassStmt::MODIFIER_FINAL;
    }

    private function yyn152() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_PropertyStmtProperty(array('name' => substr($this->yyastk[$this->yysp-(3-3)], 1), 'default' => null)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn153() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_PropertyStmtProperty(array('name' => substr($this->yyastk[$this->yysp-(5-3)], 1), 'default' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn154() {
         $this->yyval = array(new Node_PropertyStmtProperty(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1), 'default' => null)));
    }

    private function yyn155() {
         $this->yyval = array(new Node_PropertyStmtProperty(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1), 'default' => $this->yyastk[$this->yysp-(3-3)])));
    }

    private function yyn156() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_ClassConstStmtConst(array('name' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)])); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn157() {
         $this->yyval = array(new Node_ClassConstStmtConst(array('name' => $this->yyastk[$this->yysp-(4-2)], 'value' => $this->yyastk[$this->yysp-(4-4)])));
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
         $this->yyval = new Node_ListExpr(array('assignList' => $this->yyastk[$this->yysp-(6-3)], 'expr' => $this->yyastk[$this->yysp-(6-6)]));
    }

    private function yyn163() {
         $this->yyval = new Node_AssignExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn164() {
         $this->yyval = new Node_AssignRefExpr(array('var' => $this->yyastk[$this->yysp-(4-1)], 'refVar' => $this->yyastk[$this->yysp-(4-4)]));
    }

    private function yyn165() {
         $this->yyval = new Node_AssignExpr(array('var' => $this->yyastk[$this->yysp-(6-1)], 'expr' => new Node_NewExpr(array('class' => $this->yyastk[$this->yysp-(6-5)], 'args' => $this->yyastk[$this->yysp-(6-6)]))));
    }

    private function yyn166() {
         $this->yyval = new Node_NewExpr(array('class' => $this->yyastk[$this->yysp-(3-2)], 'args' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn167() {
         $this->yyval = new Node_CloneExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn168() {
         $this->yyval = new Node_AssignPlusExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn169() {
         $this->yyval = new Node_AssignMinusExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn170() {
         $this->yyval = new Node_AssignMulExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn171() {
         $this->yyval = new Node_AssignDivExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn172() {
         $this->yyval = new Node_AssignConcatExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn173() {
         $this->yyval = new Node_AssignModExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn174() {
         $this->yyval = new Node_AssignBinAndExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn175() {
         $this->yyval = new Node_AssignBinOrExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn176() {
         $this->yyval = new Node_AssignBinXorExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn177() {
         $this->yyval = new Node_AssignShiftLeftExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn178() {
         $this->yyval = new Node_AssignShiftRightExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'expr' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn179() {
         $this->yyval = new Node_PostIncExpr(array('var' => $this->yyastk[$this->yysp-(2-1)]));
    }

    private function yyn180() {
         $this->yyval = new Node_PreIncExpr(array('var' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn181() {
         $this->yyval = new Node_PostDecExpr(array('var' => $this->yyastk[$this->yysp-(2-1)]));
    }

    private function yyn182() {
         $this->yyval = new Node_PreDecExpr(array('var' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn183() {
         $this->yyval = new Node_BooleanOrExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn184() {
         $this->yyval = new Node_BooleanAndExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn185() {
         $this->yyval = new Node_LogicalOrExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn186() {
         $this->yyval = new Node_LogicalAndExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn187() {
         $this->yyval = new Node_LogicalXorExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn188() {
         $this->yyval = new Node_BinaryOrExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn189() {
         $this->yyval = new Node_BinaryAndExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn190() {
         $this->yyval = new Node_BinaryXorExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn191() {
         $this->yyval = new Node_ConcatExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn192() {
         $this->yyval = new Node_PlusExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn193() {
         $this->yyval = new Node_MinusExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn194() {
         $this->yyval = new Node_MulExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn195() {
         $this->yyval = new Node_DivExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn196() {
         $this->yyval = new Node_ModExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn197() {
         $this->yyval = new Node_ShiftLeftExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn198() {
         $this->yyval = new Node_ShiftRightExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn199() {
         $this->yyval = new Node_UnaryPlusExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn200() {
         $this->yyval = new Node_UnaryMinusExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn201() {
         $this->yyval = new Node_BooleanNotExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn202() {
         $this->yyval = new Node_BinaryNotExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn203() {
         $this->yyval = new Node_IdenticalExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn204() {
         $this->yyval = new Node_NotIdenticalExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn205() {
         $this->yyval = new Node_EqualExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn206() {
         $this->yyval = new Node_NotEqualExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn207() {
         $this->yyval = new Node_SmallerExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn208() {
         $this->yyval = new Node_SmallerOrEqualExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn209() {
         $this->yyval = new Node_GreaterExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn210() {
         $this->yyval = new Node_GreaterOrEqualExpr(array('left' => $this->yyastk[$this->yysp-(3-1)], 'right' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn211() {
         $this->yyval = new Node_InstanceOfExpr(array('expr' => $this->yyastk[$this->yysp-(3-1)], 'class' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn212() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)];
    }

    private function yyn213() {
         $this->yyval = new Node_TernaryExpr(array('cond' => $this->yyastk[$this->yysp-(5-1)], 'if' => $this->yyastk[$this->yysp-(5-3)], 'else' => $this->yyastk[$this->yysp-(5-5)]));
    }

    private function yyn214() {
         $this->yyval = new Node_TernaryExpr(array('cond' => $this->yyastk[$this->yysp-(4-1)], 'if' => null, 'else' => $this->yyastk[$this->yysp-(4-4)]));
    }

    private function yyn215() {
         $this->yyval = new Node_IssetExpr(array('vars' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn216() {
         $this->yyval = new Node_EmptyExpr(array('var' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn217() {
         $this->yyval = new Node_IncludeExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_IncludeExpr::TYPE_INCLUDE));
    }

    private function yyn218() {
         $this->yyval = new Node_IncludeExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_IncludeExpr::TYPE_INCLUDE_ONCE));
    }

    private function yyn219() {
         $this->yyval = new Node_EvalExpr(array('expr' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn220() {
         $this->yyval = new Node_IncludeExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_IncludeExpr::TYPE_REQUIRE));
    }

    private function yyn221() {
         $this->yyval = new Node_IncludeExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)], 'type' => Node_IncludeExpr::TYPE_REQUIRE_ONCE));
    }

    private function yyn222() {
         $this->yyval = new Node_IntCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn223() {
         $this->yyval = new Node_DoubleCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn224() {
         $this->yyval = new Node_StringCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn225() {
         $this->yyval = new Node_ArrayCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn226() {
         $this->yyval = new Node_ObjectCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn227() {
         $this->yyval = new Node_BoolCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn228() {
         $this->yyval = new Node_UnsetCastExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn229() {
         $this->yyval = new Node_ExitExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn230() {
         $this->yyval = new Node_ErrorSupressExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn231() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn232() {
         $this->yyval = new Node_ArrayExpr(array('items' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn233() {
         $this->yyval = new Node_ShellExecExpr(array('expr' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn234() {
         $this->yyval = new Node_PrintExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn235() {
         $this->yyval = new Node_LambdaFuncExpr(array('byRef' => $this->yyastk[$this->yysp-(9-2)], 'params' => $this->yyastk[$this->yysp-(9-4)], 'useVars' => $this->yyastk[$this->yysp-(9-6)], 'stmts' => $this->yyastk[$this->yysp-(9-8)]));
    }

    private function yyn236() {
         $this->yyval = array();
    }

    private function yyn237() {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)];
    }

    private function yyn238() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_LambdaFuncExprUse(array('var' => substr($this->yyastk[$this->yysp-(4-4)], 1), 'byRef' => $this->yyastk[$this->yysp-(4-3)])); $this->yyval = $this->yyastk[$this->yysp-(4-1)];
    }

    private function yyn239() {
         $this->yyval = array(new Node_LambdaFuncExprUse(array('var' => substr($this->yyastk[$this->yysp-(2-2)], 1), 'byRef' => $this->yyastk[$this->yysp-(2-1)])));
    }

    private function yyn240() {
         $this->yyval = new Node_FuncCallExpr(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn241() {
         $this->yyval = new Node_StaticCallExpr(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn242() {
         $this->yyval = new Node_StaticCallExpr(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn243() {
         $this->yyval = new Node_StaticCallExpr(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn244() {
         $this->yyval = new Node_StaticCallExpr(array('class' => $this->yyastk[$this->yysp-(6-1)], 'func' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn245() {
         $this->yyval = new Node_FuncCallExpr(array('func' => $this->yyastk[$this->yysp-(4-1)], 'args' => $this->yyastk[$this->yysp-(4-3)]));
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
         $this->yyval = new Node_PropertyFetchExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
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
         $this->yyval = new Node_LNumberScalar(array('value' => (int) $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn264() {
         $this->yyval = new Node_DNumberScalar(array('value' => (double) $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn265() {
         $this->yyval = new Node_StringScalar(array('value' => str_replace(array('\\\'', '\\\\'), array('\'', '\\'), substr($this->yyastk[$this->yysp-(1-1)], 1, -1))));
    }

    private function yyn266() {
         $this->yyval = new Node_LineConstScalar(array());
    }

    private function yyn267() {
         $this->yyval = new Node_FileConstScalar(array());
    }

    private function yyn268() {
         $this->yyval = new Node_DirConstScalar(array());
    }

    private function yyn269() {
         $this->yyval = new Node_ClassConstScalar(array());
    }

    private function yyn270() {
         $this->yyval = new Node_MethodConstScalar(array());
    }

    private function yyn271() {
         $this->yyval = new Node_FuncConstScalar(array());
    }

    private function yyn272() {
         $this->yyval = new Node_NSConstScalar(array());
    }

    private function yyn273() {
         $this->yyval = new Node_StringScalar(array('value' => stripcslashes($this->yyastk[$this->yysp-(3-2)])));
    }

    private function yyn274() {
         $this->yyval = new Node_StringScalar(array('value' => ''));
    }

    private function yyn275() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn276() {
         $this->yyval = new Node_ConstFetchExpr(array('name' => $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn277() {
         $this->yyval = new Node_UnaryPlusExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn278() {
         $this->yyval = new Node_UnaryMinusExpr(array('expr' => $this->yyastk[$this->yysp-(2-2)]));
    }

    private function yyn279() {
         $this->yyval = new Node_ArrayExpr(array('items' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn280() {
         $this->yyval = new Node_ClassConstFetchExpr(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn281() {
         $this->yyval = new Node_StringScalar(array('value' => $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn282() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn283() {
         $this->yyval = new Node_ConstFetchExpr(array('name' => $this->yyastk[$this->yysp-(1-1)]));
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
    private function yyn289() {}
    private function yyn290() {}

    private function yyn291() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn292() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn293() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)));
    }

    private function yyn294() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false)));
    }

    private function yyn295() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn296() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn297() {
         $this->yyval = new Node_MethodCallExpr(array('var' => $this->yyastk[$this->yysp-(6-1)], 'name' => $this->yyastk[$this->yysp-(6-3)], 'args' => $this->yyastk[$this->yysp-(6-5)]));
    }

    private function yyn298() {
         $this->yyval = new Node_PropertyFetchExpr(array('var' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
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
         $this->yyval = new Node_StaticPropertyFetchExpr(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn305() {
         $this->yyval = new Node_StaticPropertyFetchExpr(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn306() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn307() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn308() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)));
    }

    private function yyn309() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn310() {
         $this->yyval = null;
    }

    private function yyn311() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn312() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn313() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn314() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn315() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => $this->yyastk[$this->yysp-(4-1)], 'dim' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn316() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn317() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)];
    }

    private function yyn318() {
         $this->yyastk[$this->yysp-(3-1)][] = $this->yyastk[$this->yysp-(3-3)]; $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn319() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]);
    }

    private function yyn320() {
         $this->yyval = $this->yyastk[$this->yysp-(1-1)];
    }

    private function yyn321() {
         $this->yyval = $this->yyastk[$this->yysp-(4-3)];
    }

    private function yyn322() {
         $this->yyval = null;
    }

    private function yyn323() {
         $this->yyval = array();
    }

    private function yyn324() {
         $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn325() {
         $this->yyastk[$this->yysp-(5-1)][] = new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(5-3)], 'value' => $this->yyastk[$this->yysp-(5-5)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(5-1)];
    }

    private function yyn326() {
         $this->yyastk[$this->yysp-(3-1)][] = new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)); $this->yyval = $this->yyastk[$this->yysp-(3-1)];
    }

    private function yyn327() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(3-1)], 'value' => $this->yyastk[$this->yysp-(3-3)], 'byRef' => false)));
    }

    private function yyn328() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(1-1)], 'byRef' => false)));
    }

    private function yyn329() {
         $this->yyastk[$this->yysp-(6-1)][] = new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(6-3)], 'value' => $this->yyastk[$this->yysp-(6-6)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(6-1)];
    }

    private function yyn330() {
         $this->yyastk[$this->yysp-(4-1)][] = new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true)); $this->yyval = $this->yyastk[$this->yysp-(4-1)];
    }

    private function yyn331() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => $this->yyastk[$this->yysp-(4-1)], 'value' => $this->yyastk[$this->yysp-(4-4)], 'byRef' => true)));
    }

    private function yyn332() {
         $this->yyval = array(new Node_ArrayExprItem(array('key' => null, 'value' => $this->yyastk[$this->yysp-(2-2)], 'byRef' => true)));
    }

    private function yyn333() {
         $this->yyastk[$this->yysp-(2-1)][] = $this->yyastk[$this->yysp-(2-2)]; $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn334() {
         $this->yyastk[$this->yysp-(2-1)][] = stripcslashes($this->yyastk[$this->yysp-(2-2)]); $this->yyval = $this->yyastk[$this->yysp-(2-1)];
    }

    private function yyn335() {
         $this->yyval = array($this->yyastk[$this->yysp-(1-1)]);
    }

    private function yyn336() {
         $this->yyval = array(stripcslashes($this->yyastk[$this->yysp-(2-1)]), $this->yyastk[$this->yysp-(2-2)]);
    }

    private function yyn337() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)));
    }

    private function yyn338() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(4-1)], 1))), 'dim' => $this->yyastk[$this->yysp-(4-3)]));
    }

    private function yyn339() {
         $this->yyval = new Node_PropertyFetchExpr(array('var' => new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(3-1)], 1))), 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn340() {
         $this->yyval = new Node_Variable(array('name' => $this->yyastk[$this->yysp-(3-2)]));
    }

    private function yyn341() {
         $this->yyval = new Node_ArrayDimFetchExpr(array('var' => new Node_Variable(array('name' => $this->yyastk[$this->yysp-(6-2)])), 'dim' => $this->yyastk[$this->yysp-(6-4)]));
    }

    private function yyn342() {
         $this->yyval = $this->yyastk[$this->yysp-(3-2)];
    }

    private function yyn343() {
         $this->yyval = new Node_StringScalar(array('value' => $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn344() {
         $this->yyval = new Node_LNumberScalar(array('value' => (int) $this->yyastk[$this->yysp-(1-1)]));
    }

    private function yyn345() {
         $this->yyval = new Node_Variable(array('name' => substr($this->yyastk[$this->yysp-(1-1)], 1)));
    }

    private function yyn346() {
         $this->yyval = new Node_ClassConstFetchExpr(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }

    private function yyn347() {
         $this->yyval = new Node_ClassConstFetchExpr(array('class' => $this->yyastk[$this->yysp-(3-1)], 'name' => $this->yyastk[$this->yysp-(3-3)]));
    }
}
