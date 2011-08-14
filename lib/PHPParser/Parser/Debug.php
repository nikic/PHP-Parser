<?php

/* Prototype file of an object oriented PHP parser.
 * Written by Moriyoshi Koizumi, based on the work by Masato Bito.
 * This file is PUBLIC DOMAIN.
 */
class PHPParser_Parser_Debug extends PHPParser_Parser
{
    protected static $yyproduction = array(
        "start : start",
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
        "non_empty_static_array_pair_list : non_empty_static_array_pair_list ',' static_array_pair",
        "non_empty_static_array_pair_list : static_array_pair",
        "static_array_pair : static_scalar T_DOUBLE_ARROW static_scalar",
        "static_array_pair : static_scalar",
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
        "non_empty_array_pair_list : non_empty_array_pair_list ',' array_pair",
        "non_empty_array_pair_list : array_pair",
        "array_pair : expr T_DOUBLE_ARROW expr",
        "array_pair : expr",
        "array_pair : expr T_DOUBLE_ARROW '&' variable",
        "array_pair : '&' variable",
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

    protected function yyprintln($msg) {
        echo $msg, "\n";
    }

    protected function YYTRACE_NEWSTATE($state, $sym) {
        $this->yyprintln(
            '% State ' . $state
          . ', Lookahead ' . ($sym < 0 ? '--none--' : self::$yyterminals[$sym])
        );
    }

    protected function YYTRACE_READ($sym) {
        $this->yyprintln('% Reading ' . self::$yyterminals[$sym]);
    }

    protected function YYTRACE_SHIFT($sym) {
        $this->yyprintln('% Shift ' . self::$yyterminals[$sym]);
    }

    protected function YYTRACE_ACCEPT() {
        $this->yyprintln('% Accepted.');
    }

    protected function YYTRACE_REDUCE($n) {
        $this->yyprintln('% Reduce by (' . $n . ') ' . self::$yyproduction[$n]);
    }

    protected function YYTRACE_POP($state) {
        $this->yyprintln('% Recovering, uncovers state ' . $state);
    }

    protected function YYTRACE_DISCARD($sym) {
        $this->yyprintln('% Discard ' . self::$yyterminals[$sym]);
    }

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
}
