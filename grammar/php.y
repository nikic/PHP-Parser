%pure_parser
%expect 2

%right T_VOID_CAST
%right T_THROW
%left T_INCLUDE T_INCLUDE_ONCE T_EVAL T_REQUIRE T_REQUIRE_ONCE
%left ','
%left T_LOGICAL_OR
%left T_LOGICAL_XOR
%left T_LOGICAL_AND
%right T_PRINT
%right T_YIELD
%right T_DOUBLE_ARROW
%right T_YIELD_FROM
%left '=' T_PLUS_EQUAL T_MINUS_EQUAL T_MUL_EQUAL T_DIV_EQUAL T_CONCAT_EQUAL T_MOD_EQUAL T_AND_EQUAL T_OR_EQUAL T_XOR_EQUAL T_SL_EQUAL T_SR_EQUAL T_POW_EQUAL T_COALESCE_EQUAL
%left '?' ':'
%right T_COALESCE
%left T_BOOLEAN_OR
%left T_BOOLEAN_AND
%left '|'
%left '^'
%left T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
%nonassoc T_IS_EQUAL T_IS_NOT_EQUAL T_IS_IDENTICAL T_IS_NOT_IDENTICAL T_SPACESHIP
%nonassoc '<' T_IS_SMALLER_OR_EQUAL '>' T_IS_GREATER_OR_EQUAL
#if PHP7
%left T_SL T_SR
%left '+' '-' '.'
#endif
#if PHP8
%left T_PIPE
%left '.'
%left T_SL T_SR
%left '+' '-'
#endif
%left '*' '/' '%'
%right '!'
%nonassoc T_INSTANCEOF
%right '~' T_INC T_DEC T_INT_CAST T_DOUBLE_CAST T_STRING_CAST T_ARRAY_CAST T_OBJECT_CAST T_BOOL_CAST T_UNSET_CAST '@'
%right T_POW
%right '['
%nonassoc T_NEW T_CLONE
%token T_EXIT
%token T_IF
%left T_ELSEIF
%left T_ELSE
%left T_ENDIF
%token T_LNUMBER
%token T_DNUMBER
%token T_STRING
%token T_STRING_VARNAME
%token T_VARIABLE
%token T_NUM_STRING
%token T_INLINE_HTML
%token T_ENCAPSED_AND_WHITESPACE
%token T_CONSTANT_ENCAPSED_STRING
%token T_ECHO
%token T_DO
%token T_WHILE
%token T_ENDWHILE
%token T_FOR
%token T_ENDFOR
%token T_FOREACH
%token T_ENDFOREACH
%token T_DECLARE
%token T_ENDDECLARE
%token T_AS
%token T_SWITCH
%token T_MATCH
%token T_ENDSWITCH
%token T_CASE
%token T_DEFAULT
%token T_BREAK
%token T_CONTINUE
%token T_GOTO
%token T_FUNCTION
%token T_FN
%token T_CONST
%token T_RETURN
%token T_TRY
%token T_CATCH
%token T_FINALLY
%token T_THROW
%token T_USE
%token T_INSTEADOF
%token T_GLOBAL
%token T_STATIC T_ABSTRACT T_FINAL T_PRIVATE T_PROTECTED T_PUBLIC T_READONLY
%token T_PUBLIC_SET
%token T_PROTECTED_SET
%token T_PRIVATE_SET
%token T_VAR
%token T_UNSET
%token T_ISSET
%token T_EMPTY
%token T_HALT_COMPILER
%token T_CLASS
%token T_TRAIT
%token T_INTERFACE
%token T_ENUM
%token T_EXTENDS
%token T_IMPLEMENTS
%token T_OBJECT_OPERATOR
%token T_NULLSAFE_OBJECT_OPERATOR
%token T_DOUBLE_ARROW
%token T_LIST
%token T_ARRAY
%token T_CALLABLE
%token T_CLASS_C
%token T_TRAIT_C
%token T_METHOD_C
%token T_FUNC_C
%token T_PROPERTY_C
%token T_LINE
%token T_FILE
%token T_START_HEREDOC
%token T_END_HEREDOC
%token T_DOLLAR_OPEN_CURLY_BRACES
%token T_CURLY_OPEN
%token T_PAAMAYIM_NEKUDOTAYIM
%token T_NAMESPACE
%token T_NS_C
%token T_DIR
%token T_NS_SEPARATOR
%token T_ELLIPSIS
%token T_NAME_FULLY_QUALIFIED
%token T_NAME_QUALIFIED
%token T_NAME_RELATIVE
%token T_ATTRIBUTE
%token T_ENUM

%%

start:
    top_statement_list                                      { $$ = $this->handleNamespaces($1); }
;

top_statement_list_ex:
      top_statement_list_ex top_statement                   { pushNormalizing($1, $2); }
    | /* empty */                                           { init(); }
;

top_statement_list:
      top_statement_list_ex
          { makeZeroLengthNop($nop);
            if ($nop !== null) { $1[] = $nop; } $$ = $1; }
;

ampersand:
      T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
    | T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG
;

reserved_non_modifiers:
      T_INCLUDE | T_INCLUDE_ONCE | T_EVAL | T_REQUIRE | T_REQUIRE_ONCE | T_LOGICAL_OR | T_LOGICAL_XOR | T_LOGICAL_AND
    | T_INSTANCEOF | T_NEW | T_CLONE | T_EXIT | T_IF | T_ELSEIF | T_ELSE | T_ENDIF | T_DO | T_WHILE
    | T_ENDWHILE | T_FOR | T_ENDFOR | T_FOREACH | T_ENDFOREACH | T_DECLARE | T_ENDDECLARE | T_AS | T_TRY | T_CATCH
    | T_FINALLY | T_THROW | T_USE | T_INSTEADOF | T_GLOBAL | T_VAR | T_UNSET | T_ISSET | T_EMPTY | T_CONTINUE | T_GOTO
    | T_FUNCTION | T_CONST | T_RETURN | T_PRINT | T_YIELD | T_LIST | T_SWITCH | T_ENDSWITCH | T_CASE | T_DEFAULT
    | T_BREAK | T_ARRAY | T_CALLABLE | T_EXTENDS | T_IMPLEMENTS | T_NAMESPACE | T_TRAIT | T_INTERFACE | T_CLASS
    | T_CLASS_C | T_TRAIT_C | T_FUNC_C | T_METHOD_C | T_LINE | T_FILE | T_DIR | T_NS_C | T_FN
    | T_MATCH | T_ENUM
    | T_ECHO { $$ = $1; if ($$ === "<?=") $this->emitError(new Error('Cannot use "<?=" as an identifier', attributes())); }
;

semi_reserved:
      reserved_non_modifiers
    | T_STATIC | T_ABSTRACT | T_FINAL | T_PRIVATE | T_PROTECTED | T_PUBLIC | T_READONLY
;

identifier_maybe_reserved:
      T_STRING                                              { $$ = Node\Identifier[$1]; }
    | semi_reserved                                         { $$ = Node\Identifier[$1]; }
;

identifier_not_reserved:
      T_STRING                                              { $$ = Node\Identifier[$1]; }
;

reserved_non_modifiers_identifier:
      reserved_non_modifiers                                { $$ = Node\Identifier[$1]; }
;

namespace_declaration_name:
      T_STRING                                              { $$ = Name[$1]; }
    | semi_reserved                                         { $$ = Name[$1]; }
    | T_NAME_QUALIFIED                                      { $$ = Name[$1]; }
;

namespace_name:
      T_STRING                                              { $$ = Name[$1]; }
    | T_NAME_QUALIFIED                                      { $$ = Name[$1]; }
;

legacy_namespace_name:
      namespace_name
    | T_NAME_FULLY_QUALIFIED                                { $$ = Name[substr($1, 1)]; }
;

plain_variable:
      T_VARIABLE                                            { $$ = Expr\Variable[parseVar($1)]; }
;

semi:
      ';'                                                   { /* nothing */ }
    | error                                                 { /* nothing */ }
;

no_comma:
      /* empty */ { /* nothing */ }
    | ',' { $this->emitError(new Error('A trailing comma is not allowed here', attributes())); }
;

optional_comma:
      /* empty */
    | ','
;

attribute_decl:
      class_name                                            { $$ = Node\Attribute[$1, []]; }
    | class_name argument_list                              { $$ = Node\Attribute[$1, $2]; }
;

attribute_group:
      attribute_decl                                        { init($1); }
    | attribute_group ',' attribute_decl                    { push($1, $3); }
;

attribute:
      T_ATTRIBUTE attribute_group optional_comma ']'        { $$ = Node\AttributeGroup[$2]; }
;

attributes:
      attribute                                             { init($1); }
    | attributes attribute                                  { push($1, $2); }
;

optional_attributes:
      /* empty */                                           { $$ = []; }
    | attributes
;

top_statement:
      statement
    | function_declaration_statement
    | class_declaration_statement
    | T_HALT_COMPILER '(' ')' ';'
          { $$ = Stmt\HaltCompiler[$this->handleHaltCompiler()]; }
    | T_NAMESPACE namespace_declaration_name semi
          { $$ = Stmt\Namespace_[$2, null];
            $$->setAttribute('kind', Stmt\Namespace_::KIND_SEMICOLON);
            $this->checkNamespace($$); }
    | T_NAMESPACE namespace_declaration_name '{' top_statement_list '}'
          { $$ = Stmt\Namespace_[$2, $4];
            $$->setAttribute('kind', Stmt\Namespace_::KIND_BRACED);
            $this->checkNamespace($$); }
    | T_NAMESPACE '{' top_statement_list '}'
          { $$ = Stmt\Namespace_[null, $3];
            $$->setAttribute('kind', Stmt\Namespace_::KIND_BRACED);
            $this->checkNamespace($$); }
    | T_USE use_declarations semi                           { $$ = Stmt\Use_[$2, Stmt\Use_::TYPE_NORMAL]; }
    | T_USE use_type use_declarations semi                  { $$ = Stmt\Use_[$3, $2]; }
    | group_use_declaration
    | T_CONST constant_declaration_list semi                { $$ = new Stmt\Const_($2, attributes(), []); }
    | attributes T_CONST constant_declaration_list semi
          { $$ = new Stmt\Const_($3, attributes(), $1);
            $this->checkConstantAttributes($$); }
;

use_type:
      T_FUNCTION                                            { $$ = Stmt\Use_::TYPE_FUNCTION; }
    | T_CONST                                               { $$ = Stmt\Use_::TYPE_CONSTANT; }
;

group_use_declaration:
      T_USE use_type legacy_namespace_name T_NS_SEPARATOR '{' unprefixed_use_declarations '}' semi
          { $$ = Stmt\GroupUse[$3, $6, $2]; }
    | T_USE legacy_namespace_name T_NS_SEPARATOR '{' inline_use_declarations '}' semi
          { $$ = Stmt\GroupUse[$2, $5, Stmt\Use_::TYPE_UNKNOWN]; }
;

unprefixed_use_declarations:
      non_empty_unprefixed_use_declarations optional_comma
;

non_empty_unprefixed_use_declarations:
      non_empty_unprefixed_use_declarations ',' unprefixed_use_declaration
          { push($1, $3); }
    | unprefixed_use_declaration                            { init($1); }
;

use_declarations:
      non_empty_use_declarations no_comma
;

non_empty_use_declarations:
      non_empty_use_declarations ',' use_declaration        { push($1, $3); }
    | use_declaration                                       { init($1); }
;

inline_use_declarations:
      non_empty_inline_use_declarations optional_comma
;

non_empty_inline_use_declarations:
      non_empty_inline_use_declarations ',' inline_use_declaration
          { push($1, $3); }
    | inline_use_declaration                                { init($1); }
;

unprefixed_use_declaration:
      namespace_name
          { $$ = Node\UseItem[$1, null, Stmt\Use_::TYPE_UNKNOWN]; $this->checkUseUse($$, #1); }
    | namespace_name T_AS identifier_not_reserved
          { $$ = Node\UseItem[$1, $3, Stmt\Use_::TYPE_UNKNOWN]; $this->checkUseUse($$, #3); }
;

use_declaration:
      legacy_namespace_name
          { $$ = Node\UseItem[$1, null, Stmt\Use_::TYPE_UNKNOWN]; $this->checkUseUse($$, #1); }
    | legacy_namespace_name T_AS identifier_not_reserved
          { $$ = Node\UseItem[$1, $3, Stmt\Use_::TYPE_UNKNOWN]; $this->checkUseUse($$, #3); }
;

inline_use_declaration:
      unprefixed_use_declaration                            { $$ = $1; $$->type = Stmt\Use_::TYPE_NORMAL; }
    | use_type unprefixed_use_declaration                   { $$ = $2; $$->type = $1; }
;

constant_declaration_list:
      non_empty_constant_declaration_list no_comma
;

non_empty_constant_declaration_list:
      non_empty_constant_declaration_list ',' constant_declaration
          { push($1, $3); }
    | constant_declaration                                  { init($1); }
;

constant_declaration:
    identifier_not_reserved '=' expr                        { $$ = Node\Const_[$1, $3]; }
;

class_const_list:
      non_empty_class_const_list no_comma
;

non_empty_class_const_list:
      non_empty_class_const_list ',' class_const            { push($1, $3); }
    | class_const                                           { init($1); }
;

class_const:
      T_STRING '=' expr
          { $$ = Node\Const_[new Node\Identifier($1, stackAttributes(#1)), $3]; }
    | semi_reserved '=' expr
          { $$ = Node\Const_[new Node\Identifier($1, stackAttributes(#1)), $3]; }
;

inner_statement_list_ex:
      inner_statement_list_ex inner_statement               { pushNormalizing($1, $2); }
    | /* empty */                                           { init(); }
;

inner_statement_list:
      inner_statement_list_ex
          { makeZeroLengthNop($nop);
            if ($nop !== null) { $1[] = $nop; } $$ = $1; }
;

inner_statement:
      statement
    | function_declaration_statement
    | class_declaration_statement
    | T_HALT_COMPILER
          { throw new Error('__HALT_COMPILER() can only be used from the outermost scope', attributes()); }
;

non_empty_statement:
      '{' inner_statement_list '}'                          { $$ = Stmt\Block[$2]; }
    | T_IF '(' expr ')' blocklike_statement elseif_list else_single
          { $$ = Stmt\If_[$3, ['stmts' => $5, 'elseifs' => $6, 'else' => $7]]; }
    | T_IF '(' expr ')' ':' inner_statement_list new_elseif_list new_else_single T_ENDIF ';'
          { $$ = Stmt\If_[$3, ['stmts' => $6, 'elseifs' => $7, 'else' => $8]]; }
    | T_WHILE '(' expr ')' while_statement                  { $$ = Stmt\While_[$3, $5]; }
    | T_DO blocklike_statement T_WHILE '(' expr ')' ';'     { $$ = Stmt\Do_   [$5, $2]; }
    | T_FOR '(' for_expr ';'  for_expr ';' for_expr ')' for_statement
          { $$ = Stmt\For_[['init' => $3, 'cond' => $5, 'loop' => $7, 'stmts' => $9]]; }
    | T_SWITCH '(' expr ')' switch_case_list                { $$ = Stmt\Switch_[$3, $5]; }
    | T_BREAK optional_expr semi                            { $$ = Stmt\Break_[$2]; }
    | T_CONTINUE optional_expr semi                         { $$ = Stmt\Continue_[$2]; }
    | T_RETURN optional_expr semi                           { $$ = Stmt\Return_[$2]; }
    | T_GLOBAL global_var_list semi                         { $$ = Stmt\Global_[$2]; }
    | T_STATIC static_var_list semi                         { $$ = Stmt\Static_[$2]; }
    | T_ECHO expr_list_forbid_comma semi                    { $$ = Stmt\Echo_[$2]; }
    | T_INLINE_HTML {
        $$ = Stmt\InlineHTML[$1];
        $$->setAttribute('hasLeadingNewline', $this->inlineHtmlHasLeadingNewline(#1));
    }
    | expr semi                                             { $$ = Stmt\Expression[$1]; }
    | T_UNSET '(' variables_list ')' semi                   { $$ = Stmt\Unset_[$3]; }
    | T_FOREACH '(' expr T_AS foreach_variable ')' foreach_statement
          { $$ = Stmt\Foreach_[$3, $5[0], ['keyVar' => null, 'byRef' => $5[1], 'stmts' => $7]]; }
    | T_FOREACH '(' expr T_AS variable T_DOUBLE_ARROW foreach_variable ')' foreach_statement
          { $$ = Stmt\Foreach_[$3, $7[0], ['keyVar' => $5, 'byRef' => $7[1], 'stmts' => $9]]; }
    | T_FOREACH '(' expr error ')' foreach_statement
          { $$ = Stmt\Foreach_[$3, new Expr\Error(stackAttributes(#4)), ['stmts' => $6]]; }
    | T_DECLARE '(' declare_list ')' declare_statement      { $$ = Stmt\Declare_[$3, $5]; }
    | T_TRY '{' inner_statement_list '}' catches optional_finally
          { $$ = Stmt\TryCatch[$3, $5, $6]; $this->checkTryCatch($$); }
    | T_GOTO identifier_not_reserved semi                   { $$ = Stmt\Goto_[$2]; }
    | identifier_not_reserved ':'                           { $$ = Stmt\Label[$1]; }
    | error                                                 { $$ = null; /* means: no statement */ }
;

statement:
      non_empty_statement
    | ';'                                                   { makeNop($$); }
;

blocklike_statement:
     statement                                              { toBlock($1); }
;

catches:
      /* empty */                                           { init(); }
    | catches catch                                         { push($1, $2); }
;

name_union:
      name                                                  { init($1); }
    | name_union '|' name                                   { push($1, $3); }
;

catch:
    T_CATCH '(' name_union optional_plain_variable ')' '{' inner_statement_list '}'
        { $$ = Stmt\Catch_[$3, $4, $7]; }
;

optional_finally:
      /* empty */                                           { $$ = null; }
    | T_FINALLY '{' inner_statement_list '}'                { $$ = Stmt\Finally_[$3]; }
;

variables_list:
      non_empty_variables_list optional_comma
;

non_empty_variables_list:
      variable                                              { init($1); }
    | non_empty_variables_list ',' variable                 { push($1, $3); }
;

optional_ref:
      /* empty */                                           { $$ = false; }
    | ampersand                                             { $$ = true; }
;

optional_arg_ref:
      /* empty */                                           { $$ = false; }
    | T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG                 { $$ = true; }
;

optional_ellipsis:
      /* empty */                                           { $$ = false; }
    | T_ELLIPSIS                                            { $$ = true; }
;

block_or_error:
      '{' inner_statement_list '}'                          { $$ = $2; }
    | error                                                 { $$ = []; }
;

fn_identifier:
      identifier_not_reserved
    | T_READONLY                                            { $$ = Node\Identifier[$1]; }
    | T_EXIT                                                { $$ = Node\Identifier[$1]; }
    | T_CLONE                                               { $$ = Node\Identifier[$1]; }
;

function_declaration_statement:
      T_FUNCTION optional_ref fn_identifier '(' parameter_list ')' optional_return_type block_or_error
          { $$ = Stmt\Function_[$3, ['byRef' => $2, 'params' => $5, 'returnType' => $7, 'stmts' => $8, 'attrGroups' => []]]; }
    | attributes T_FUNCTION optional_ref fn_identifier '(' parameter_list ')' optional_return_type block_or_error
          { $$ = Stmt\Function_[$4, ['byRef' => $3, 'params' => $6, 'returnType' => $8, 'stmts' => $9, 'attrGroups' => $1]]; }
;

class_declaration_statement:
      class_entry_type identifier_not_reserved extends_from implements_list '{' class_statement_list '}'
          { $$ = Stmt\Class_[$2, ['type' => $1, 'extends' => $3, 'implements' => $4, 'stmts' => $6, 'attrGroups' => []]];
            $this->checkClass($$, #2); }
    | attributes class_entry_type identifier_not_reserved extends_from implements_list '{' class_statement_list '}'
          { $$ = Stmt\Class_[$3, ['type' => $2, 'extends' => $4, 'implements' => $5, 'stmts' => $7, 'attrGroups' => $1]];
            $this->checkClass($$, #3); }
    | optional_attributes T_INTERFACE identifier_not_reserved interface_extends_list '{' class_statement_list '}'
          { $$ = Stmt\Interface_[$3, ['extends' => $4, 'stmts' => $6, 'attrGroups' => $1]];
            $this->checkInterface($$, #3); }
    | optional_attributes T_TRAIT identifier_not_reserved '{' class_statement_list '}'
          { $$ = Stmt\Trait_[$3, ['stmts' => $5, 'attrGroups' => $1]]; }
    | optional_attributes T_ENUM identifier_not_reserved enum_scalar_type implements_list '{' class_statement_list '}'
          { $$ = Stmt\Enum_[$3, ['scalarType' => $4, 'implements' => $5, 'stmts' => $7, 'attrGroups' => $1]];
            $this->checkEnum($$, #3); }
;

enum_scalar_type:
      /* empty */                                           { $$ = null; }
    | ':' type                                              { $$ = $2; }

enum_case_expr:
      /* empty */                                           { $$ = null; }
    | '=' expr                                              { $$ = $2; }
;

class_entry_type:
      T_CLASS                                               { $$ = 0; }
    | class_modifiers T_CLASS
;

class_modifiers:
      class_modifier
    | class_modifiers class_modifier                        { $this->checkClassModifier($1, $2, #2); $$ = $1 | $2; }
;

class_modifier:
      T_ABSTRACT                                            { $$ = Modifiers::ABSTRACT; }
    | T_FINAL                                               { $$ = Modifiers::FINAL; }
    | T_READONLY                                            { $$ = Modifiers::READONLY; }
;

extends_from:
      /* empty */                                           { $$ = null; }
    | T_EXTENDS class_name                                  { $$ = $2; }
;

interface_extends_list:
      /* empty */                                           { $$ = array(); }
    | T_EXTENDS class_name_list                             { $$ = $2; }
;

implements_list:
      /* empty */                                           { $$ = array(); }
    | T_IMPLEMENTS class_name_list                          { $$ = $2; }
;

class_name_list:
      non_empty_class_name_list no_comma
;

non_empty_class_name_list:
      class_name                                            { init($1); }
    | non_empty_class_name_list ',' class_name              { push($1, $3); }
;

for_statement:
      blocklike_statement
    | ':' inner_statement_list T_ENDFOR ';'                 { $$ = $2; }
;

foreach_statement:
      blocklike_statement
    | ':' inner_statement_list T_ENDFOREACH ';'             { $$ = $2; }
;

declare_statement:
      non_empty_statement                                   { toBlock($1); }
    | ';'                                                   { $$ = null; }
    | ':' inner_statement_list T_ENDDECLARE ';'             { $$ = $2; }
;

declare_list:
      non_empty_declare_list no_comma
;

non_empty_declare_list:
      declare_list_element                                  { init($1); }
    | non_empty_declare_list ',' declare_list_element       { push($1, $3); }
;

declare_list_element:
      identifier_not_reserved '=' expr                      { $$ = Node\DeclareItem[$1, $3]; }
;

switch_case_list:
      '{' case_list '}'                                     { $$ = $2; }
    | '{' ';' case_list '}'                                 { $$ = $3; }
    | ':' case_list T_ENDSWITCH ';'                         { $$ = $2; }
    | ':' ';' case_list T_ENDSWITCH ';'                     { $$ = $3; }
;

case_list:
      /* empty */                                           { init(); }
    | case_list case                                        { push($1, $2); }
;

case:
      T_CASE expr case_separator inner_statement_list_ex    { $$ = Stmt\Case_[$2, $4]; }
    | T_DEFAULT case_separator inner_statement_list_ex      { $$ = Stmt\Case_[null, $3]; }
;

case_separator:
      ':'
    | ';'
;

match:
      T_MATCH '(' expr ')' '{' match_arm_list '}'           { $$ = Expr\Match_[$3, $6]; }
;

match_arm_list:
      /* empty */                                           { $$ = []; }
    | non_empty_match_arm_list optional_comma
;

non_empty_match_arm_list:
      match_arm                                             { init($1); }
    | non_empty_match_arm_list ',' match_arm                { push($1, $3); }
;

match_arm:
      expr_list_allow_comma T_DOUBLE_ARROW expr             { $$ = Node\MatchArm[$1, $3]; }
    | T_DEFAULT optional_comma T_DOUBLE_ARROW expr          { $$ = Node\MatchArm[null, $4]; }
;

while_statement:
      blocklike_statement                                   { $$ = $1; }
    | ':' inner_statement_list T_ENDWHILE ';'               { $$ = $2; }
;

elseif_list:
      /* empty */                                           { init(); }
    | elseif_list elseif                                    { push($1, $2); }
;

elseif:
      T_ELSEIF '(' expr ')' blocklike_statement             { $$ = Stmt\ElseIf_[$3, $5]; }
;

new_elseif_list:
      /* empty */                                           { init(); }
    | new_elseif_list new_elseif                            { push($1, $2); }
;

new_elseif:
     T_ELSEIF '(' expr ')' ':' inner_statement_list
         { $$ = Stmt\ElseIf_[$3, $6]; $this->fixupAlternativeElse($$); }
;

else_single:
      /* empty */                                           { $$ = null; }
    | T_ELSE blocklike_statement                            { $$ = Stmt\Else_[$2]; }
;

new_else_single:
      /* empty */                                           { $$ = null; }
    | T_ELSE ':' inner_statement_list
          { $$ = Stmt\Else_[$3]; $this->fixupAlternativeElse($$); }
;

foreach_variable:
      variable                                              { $$ = array($1, false); }
    | ampersand variable                                    { $$ = array($2, true); }
    | list_expr                                             { $$ = array($1, false); }
    | array_short_syntax
          { $$ = array($this->fixupArrayDestructuring($1), false); }
;

parameter_list:
      non_empty_parameter_list optional_comma
    | /* empty */                                           { $$ = array(); }
;

non_empty_parameter_list:
      parameter                                             { init($1); }
    | non_empty_parameter_list ',' parameter                { push($1, $3); }
;

optional_property_modifiers:
      /* empty */               { $$ = 0; }
    | optional_property_modifiers property_modifier
          { $this->checkModifier($1, $2, #2); $$ = $1 | $2; }
;

property_modifier:
      T_PUBLIC                  { $$ = Modifiers::PUBLIC; }
    | T_PROTECTED               { $$ = Modifiers::PROTECTED; }
    | T_PRIVATE                 { $$ = Modifiers::PRIVATE; }
    | T_PUBLIC_SET              { $$ = Modifiers::PUBLIC_SET; }
    | T_PROTECTED_SET           { $$ = Modifiers::PROTECTED_SET; }
    | T_PRIVATE_SET             { $$ = Modifiers::PRIVATE_SET; }
    | T_READONLY                { $$ = Modifiers::READONLY; }
    | T_FINAL                   { $$ = Modifiers::FINAL; }
;

parameter:
      optional_attributes optional_property_modifiers optional_type_without_static
      optional_arg_ref optional_ellipsis plain_variable optional_property_hook_list
          { $$ = new Node\Param($6, null, $3, $4, $5, attributes(), $2, $1, $7);
            $this->checkParam($$);
            $this->addPropertyNameToHooks($$); }
    | optional_attributes optional_property_modifiers optional_type_without_static
      optional_arg_ref optional_ellipsis plain_variable '=' expr optional_property_hook_list
          { $$ = new Node\Param($6, $8, $3, $4, $5, attributes(), $2, $1, $9);
            $this->checkParam($$);
            $this->addPropertyNameToHooks($$); }
    | optional_attributes optional_property_modifiers optional_type_without_static
      optional_arg_ref optional_ellipsis error
          { $$ = new Node\Param(Expr\Error[], null, $3, $4, $5, attributes(), $2, $1); }
;

type_expr:
      type
    | '?' type                                              { $$ = Node\NullableType[$2]; }
    | union_type                                            { $$ = Node\UnionType[$1]; }
    | intersection_type
;

type:
      type_without_static
    | T_STATIC                                              { $$ = Node\Name['static']; }
;

type_without_static:
      name                                                  { $$ = $this->handleBuiltinTypes($1); }
    | T_ARRAY                                               { $$ = Node\Identifier['array']; }
    | T_CALLABLE                                            { $$ = Node\Identifier['callable']; }
;

union_type_element:
      type
    | '(' intersection_type ')' { $$ = $2; }
;

union_type:
      union_type_element '|' union_type_element             { init($1, $3); }
    | union_type '|' union_type_element                     { push($1, $3); }
;

union_type_without_static_element:
                type_without_static
        |        '(' intersection_type_without_static ')' { $$ = $2; }
;

union_type_without_static:
      union_type_without_static_element '|' union_type_without_static_element   { init($1, $3); }
    | union_type_without_static '|' union_type_without_static_element           { push($1, $3); }
;

intersection_type_list:
      type T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG type   { init($1, $3); }
    | intersection_type_list T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG type
          { push($1, $3); }
;

intersection_type:
      intersection_type_list { $$ = Node\IntersectionType[$1]; }
;

intersection_type_without_static_list:
      type_without_static T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG type_without_static
          { init($1, $3); }
    | intersection_type_without_static_list T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG type_without_static
          { push($1, $3); }
;

intersection_type_without_static:
      intersection_type_without_static_list { $$ = Node\IntersectionType[$1]; }
;

type_expr_without_static:
      type_without_static
    | '?' type_without_static                               { $$ = Node\NullableType[$2]; }
    | union_type_without_static                             { $$ = Node\UnionType[$1]; }
    | intersection_type_without_static
;

optional_type_without_static:
      /* empty */                                           { $$ = null; }
    | type_expr_without_static
;

optional_return_type:
      /* empty */                                           { $$ = null; }
    | ':' type_expr                                         { $$ = $2; }
    | ':' error                                             { $$ = null; }
;

argument_list:
      '(' ')'                                               { $$ = array(); }
    | '(' non_empty_argument_list optional_comma ')'        { $$ = $2; }
    | '(' variadic_placeholder ')'                          { init($2); }
;

clone_argument_list:
      '(' ')'                                              { $$ = array(); }
    | '(' non_empty_clone_argument_list optional_comma ')' { $$ = $2; }
    | '(' expr ',' ')'                                     { init(Node\Arg[$2, false, false]); }
    | '(' variadic_placeholder ')'                         { init($2); }
;

non_empty_clone_argument_list:
		expr ',' argument
			{ init(new Node\Arg($1, false, false, stackAttributes(#1)), $3); }
	|	argument_no_expr
			{ init($1); }
	|	non_empty_clone_argument_list ',' argument
			{ push($1, $3); }
;

variadic_placeholder:
      T_ELLIPSIS                                            { $$ = Node\VariadicPlaceholder[]; }
;

non_empty_argument_list:
      argument                                              { init($1); }
    | non_empty_argument_list ',' argument                  { push($1, $3); }
;

argument_no_expr:
      ampersand variable                                    { $$ = Node\Arg[$2, true, false]; }
    | T_ELLIPSIS expr                                       { $$ = Node\Arg[$2, false, true]; }
    | identifier_maybe_reserved ':' expr
          { $$ = new Node\Arg($3, false, false, attributes(), $1); }
;

argument:
      expr                                                  { $$ = Node\Arg[$1, false, false]; }
    | argument_no_expr                                      { $$ = $1; }
;

global_var_list:
      non_empty_global_var_list no_comma
;

non_empty_global_var_list:
      non_empty_global_var_list ',' global_var              { push($1, $3); }
    | global_var                                            { init($1); }
;

global_var:
      simple_variable
;

static_var_list:
      non_empty_static_var_list no_comma
;

non_empty_static_var_list:
      non_empty_static_var_list ',' static_var              { push($1, $3); }
    | static_var                                            { init($1); }
;

static_var:
      plain_variable                                        { $$ = Node\StaticVar[$1, null]; }
    | plain_variable '=' expr                               { $$ = Node\StaticVar[$1, $3]; }
;

class_statement_list_ex:
      class_statement_list_ex class_statement               { if ($2 !== null) { push($1, $2); } else { $$ = $1; } }
    | /* empty */                                           { init(); }
;

class_statement_list:
      class_statement_list_ex
          { makeZeroLengthNop($nop);
            if ($nop !== null) { $1[] = $nop; } $$ = $1; }
;

class_statement:
      optional_attributes variable_modifiers optional_type_without_static property_declaration_list semi
          { $$ = new Stmt\Property($2, $4, attributes(), $3, $1); }
#if PHP8
    | optional_attributes variable_modifiers optional_type_without_static property_declaration_list '{' property_hook_list '}'
          { $$ = new Stmt\Property($2, $4, attributes(), $3, $1, $6);
            $this->checkPropertyHooksForMultiProperty($$, #5);
            $this->checkEmptyPropertyHookList($6, #5);
            $this->addPropertyNameToHooks($$); }
#endif
    | optional_attributes method_modifiers T_CONST class_const_list semi
          { $$ = new Stmt\ClassConst($4, $2, attributes(), $1);
            $this->checkClassConst($$, #2); }
    | optional_attributes method_modifiers T_CONST type_expr class_const_list semi
          { $$ = new Stmt\ClassConst($5, $2, attributes(), $1, $4);
            $this->checkClassConst($$, #2); }
    | optional_attributes method_modifiers T_FUNCTION optional_ref identifier_maybe_reserved '(' parameter_list ')'
      optional_return_type method_body
          { $$ = Stmt\ClassMethod[$5, ['type' => $2, 'byRef' => $4, 'params' => $7, 'returnType' => $9, 'stmts' => $10, 'attrGroups' => $1]];
            $this->checkClassMethod($$, #2); }
    | T_USE class_name_list trait_adaptations               { $$ = Stmt\TraitUse[$2, $3]; }
    | optional_attributes T_CASE identifier_maybe_reserved enum_case_expr semi
         { $$ = Stmt\EnumCase[$3, $4, $1]; }
    | error                                                 { $$ = null; /* will be skipped */ }
;

trait_adaptations:
      ';'                                                   { $$ = array(); }
    | '{' trait_adaptation_list '}'                         { $$ = $2; }
;

trait_adaptation_list:
      /* empty */                                           { init(); }
    | trait_adaptation_list trait_adaptation                { push($1, $2); }
;

trait_adaptation:
      trait_method_reference_fully_qualified T_INSTEADOF class_name_list ';'
          { $$ = Stmt\TraitUseAdaptation\Precedence[$1[0], $1[1], $3]; }
    | trait_method_reference T_AS member_modifier identifier_maybe_reserved ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], $3, $4]; }
    | trait_method_reference T_AS member_modifier ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], $3, null]; }
    | trait_method_reference T_AS identifier_not_reserved ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], null, $3]; }
    | trait_method_reference T_AS reserved_non_modifiers_identifier ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], null, $3]; }
;

trait_method_reference_fully_qualified:
      name T_PAAMAYIM_NEKUDOTAYIM identifier_maybe_reserved { $$ = array($1, $3); }
;
trait_method_reference:
      trait_method_reference_fully_qualified
    | identifier_maybe_reserved                             { $$ = array(null, $1); }
;

method_body:
      ';' /* abstract method */                             { $$ = null; }
    | block_or_error
;

variable_modifiers:
      non_empty_member_modifiers
    | T_VAR                                                 { $$ = 0; }
;

method_modifiers:
      /* empty */                                           { $$ = 0; }
    | non_empty_member_modifiers
;

non_empty_member_modifiers:
      member_modifier
    | non_empty_member_modifiers member_modifier            { $this->checkModifier($1, $2, #2); $$ = $1 | $2; }
;

member_modifier:
      T_PUBLIC                                              { $$ = Modifiers::PUBLIC; }
    | T_PROTECTED                                           { $$ = Modifiers::PROTECTED; }
    | T_PRIVATE                                             { $$ = Modifiers::PRIVATE; }
    | T_PUBLIC_SET                                          { $$ = Modifiers::PUBLIC_SET; }
    | T_PROTECTED_SET                                       { $$ = Modifiers::PROTECTED_SET; }
    | T_PRIVATE_SET                                         { $$ = Modifiers::PRIVATE_SET; }
    | T_STATIC                                              { $$ = Modifiers::STATIC; }
    | T_ABSTRACT                                            { $$ = Modifiers::ABSTRACT; }
    | T_FINAL                                               { $$ = Modifiers::FINAL; }
    | T_READONLY                                            { $$ = Modifiers::READONLY; }
;

property_declaration_list:
      non_empty_property_declaration_list no_comma
;

non_empty_property_declaration_list:
      property_declaration                                  { init($1); }
    | non_empty_property_declaration_list ',' property_declaration
          { push($1, $3); }
;

property_decl_name:
      T_VARIABLE                                            { $$ = Node\VarLikeIdentifier[parseVar($1)]; }
;

property_declaration:
      property_decl_name                                    { $$ = Node\PropertyItem[$1, null]; }
    | property_decl_name '=' expr                           { $$ = Node\PropertyItem[$1, $3]; }
;

property_hook_list:
      /* empty */                                           { $$ = []; }
    | property_hook_list property_hook                      { push($1, $2); }
;

optional_property_hook_list:
      /* empty */                                           { $$ = []; }
#if PHP8
    | '{' property_hook_list '}'                            { $$ = $2; $this->checkEmptyPropertyHookList($2, #1); }
#endif
;

property_hook:
      optional_attributes property_hook_modifiers optional_ref identifier_not_reserved property_hook_body
          { $$ = Node\PropertyHook[$4, $5, ['flags' => $2, 'byRef' => $3, 'params' => [], 'attrGroups' => $1]];
            $this->checkPropertyHook($$, null); }
    | optional_attributes property_hook_modifiers optional_ref identifier_not_reserved '(' parameter_list ')' property_hook_body
          { $$ = Node\PropertyHook[$4, $8, ['flags' => $2, 'byRef' => $3, 'params' => $6, 'attrGroups' => $1]];
            $this->checkPropertyHook($$, #5); }
;

property_hook_body:
      ';'                                                   { $$ = null; }
    | '{' inner_statement_list '}'                          { $$ = $2; }
    | T_DOUBLE_ARROW expr ';'                               { $$ = $2; }
;

property_hook_modifiers:
      /* empty */                                           { $$ = 0; }
    | property_hook_modifiers member_modifier
          { $this->checkPropertyHookModifiers($1, $2, #2); $$ = $1 | $2; }
;

expr_list_forbid_comma:
      non_empty_expr_list no_comma
;

expr_list_allow_comma:
      non_empty_expr_list optional_comma
;

non_empty_expr_list:
      non_empty_expr_list ',' expr                          { push($1, $3); }
    | expr                                                  { init($1); }
;

for_expr:
      /* empty */                                           { $$ = array(); }
    | expr_list_forbid_comma
;

expr:
      variable
    | list_expr '=' expr                                    { $$ = Expr\Assign[$1, $3]; }
    | array_short_syntax '=' expr
          { $$ = Expr\Assign[$this->fixupArrayDestructuring($1), $3]; }
    | variable '=' expr                                     { $$ = Expr\Assign[$1, $3]; }
    | variable '=' ampersand variable                       { $$ = Expr\AssignRef[$1, $4]; }
    | variable '=' ampersand new_expr
          { $$ = Expr\AssignRef[$1, $4];
            if (!$this->phpVersion->allowsAssignNewByReference()) {
                $this->emitError(new Error('Cannot assign new by reference', attributes()));
            }
          }
    | new_expr
    | match
    | T_CLONE clone_argument_list                           { $$ = Expr\FuncCall[new Node\Name($1, stackAttributes(#1)), $2]; }
    | T_CLONE expr                                          { $$ = Expr\Clone_[$2]; }
    | variable T_PLUS_EQUAL expr                            { $$ = Expr\AssignOp\Plus      [$1, $3]; }
    | variable T_MINUS_EQUAL expr                           { $$ = Expr\AssignOp\Minus     [$1, $3]; }
    | variable T_MUL_EQUAL expr                             { $$ = Expr\AssignOp\Mul       [$1, $3]; }
    | variable T_DIV_EQUAL expr                             { $$ = Expr\AssignOp\Div       [$1, $3]; }
    | variable T_CONCAT_EQUAL expr                          { $$ = Expr\AssignOp\Concat    [$1, $3]; }
    | variable T_MOD_EQUAL expr                             { $$ = Expr\AssignOp\Mod       [$1, $3]; }
    | variable T_AND_EQUAL expr                             { $$ = Expr\AssignOp\BitwiseAnd[$1, $3]; }
    | variable T_OR_EQUAL expr                              { $$ = Expr\AssignOp\BitwiseOr [$1, $3]; }
    | variable T_XOR_EQUAL expr                             { $$ = Expr\AssignOp\BitwiseXor[$1, $3]; }
    | variable T_SL_EQUAL expr                              { $$ = Expr\AssignOp\ShiftLeft [$1, $3]; }
    | variable T_SR_EQUAL expr                              { $$ = Expr\AssignOp\ShiftRight[$1, $3]; }
    | variable T_POW_EQUAL expr                             { $$ = Expr\AssignOp\Pow       [$1, $3]; }
    | variable T_COALESCE_EQUAL expr                        { $$ = Expr\AssignOp\Coalesce  [$1, $3]; }
    | variable T_INC                                        { $$ = Expr\PostInc[$1]; }
    | T_INC variable                                        { $$ = Expr\PreInc [$2]; }
    | variable T_DEC                                        { $$ = Expr\PostDec[$1]; }
    | T_DEC variable                                        { $$ = Expr\PreDec [$2]; }
    | expr T_BOOLEAN_OR expr                                { $$ = Expr\BinaryOp\BooleanOr [$1, $3]; }
    | expr T_BOOLEAN_AND expr                               { $$ = Expr\BinaryOp\BooleanAnd[$1, $3]; }
    | expr T_LOGICAL_OR expr                                { $$ = Expr\BinaryOp\LogicalOr [$1, $3]; }
    | expr T_LOGICAL_AND expr                               { $$ = Expr\BinaryOp\LogicalAnd[$1, $3]; }
    | expr T_LOGICAL_XOR expr                               { $$ = Expr\BinaryOp\LogicalXor[$1, $3]; }
    | expr '|' expr                                         { $$ = Expr\BinaryOp\BitwiseOr [$1, $3]; }
    | expr T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG expr   { $$ = Expr\BinaryOp\BitwiseAnd[$1, $3]; }
    | expr T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG expr       { $$ = Expr\BinaryOp\BitwiseAnd[$1, $3]; }
    | expr '^' expr                                         { $$ = Expr\BinaryOp\BitwiseXor[$1, $3]; }
    | expr '.' expr                                         { $$ = Expr\BinaryOp\Concat    [$1, $3]; }
    | expr '+' expr                                         { $$ = Expr\BinaryOp\Plus      [$1, $3]; }
    | expr '-' expr                                         { $$ = Expr\BinaryOp\Minus     [$1, $3]; }
    | expr '*' expr                                         { $$ = Expr\BinaryOp\Mul       [$1, $3]; }
    | expr '/' expr                                         { $$ = Expr\BinaryOp\Div       [$1, $3]; }
    | expr '%' expr                                         { $$ = Expr\BinaryOp\Mod       [$1, $3]; }
    | expr T_SL expr                                        { $$ = Expr\BinaryOp\ShiftLeft [$1, $3]; }
    | expr T_SR expr                                        { $$ = Expr\BinaryOp\ShiftRight[$1, $3]; }
    | expr T_POW expr                                       { $$ = Expr\BinaryOp\Pow       [$1, $3]; }
    | '+' expr %prec T_INC                                  { $$ = Expr\UnaryPlus [$2]; }
    | '-' expr %prec T_INC                                  { $$ = Expr\UnaryMinus[$2]; }
    | '!' expr                                              { $$ = Expr\BooleanNot[$2]; }
    | '~' expr                                              { $$ = Expr\BitwiseNot[$2]; }
    | expr T_IS_IDENTICAL expr                              { $$ = Expr\BinaryOp\Identical     [$1, $3]; }
    | expr T_IS_NOT_IDENTICAL expr                          { $$ = Expr\BinaryOp\NotIdentical  [$1, $3]; }
    | expr T_IS_EQUAL expr                                  { $$ = Expr\BinaryOp\Equal         [$1, $3]; }
    | expr T_IS_NOT_EQUAL expr                              { $$ = Expr\BinaryOp\NotEqual      [$1, $3]; }
    | expr T_SPACESHIP expr                                 { $$ = Expr\BinaryOp\Spaceship     [$1, $3]; }
    | expr '<' expr                                         { $$ = Expr\BinaryOp\Smaller       [$1, $3]; }
    | expr T_IS_SMALLER_OR_EQUAL expr                       { $$ = Expr\BinaryOp\SmallerOrEqual[$1, $3]; }
    | expr '>' expr                                         { $$ = Expr\BinaryOp\Greater       [$1, $3]; }
    | expr T_IS_GREATER_OR_EQUAL expr                       { $$ = Expr\BinaryOp\GreaterOrEqual[$1, $3]; }
#if PHP8
    | expr T_PIPE expr                                      { $$ = Expr\BinaryOp\Pipe[$1, $3]; }
#endif
    | expr T_INSTANCEOF class_name_reference                { $$ = Expr\Instanceof_[$1, $3]; }
    | '(' expr ')'                                          { $$ = $2; }
    | expr '?' expr ':' expr                                { $$ = Expr\Ternary[$1, $3,   $5]; }
    | expr '?' ':' expr                                     { $$ = Expr\Ternary[$1, null, $4]; }
    | expr T_COALESCE expr                                  { $$ = Expr\BinaryOp\Coalesce[$1, $3]; }
    | T_ISSET '(' expr_list_allow_comma ')'                 { $$ = Expr\Isset_[$3]; }
    | T_EMPTY '(' expr ')'                                  { $$ = Expr\Empty_[$3]; }
    | T_INCLUDE expr                                        { $$ = Expr\Include_[$2, Expr\Include_::TYPE_INCLUDE]; }
    | T_INCLUDE_ONCE expr                                   { $$ = Expr\Include_[$2, Expr\Include_::TYPE_INCLUDE_ONCE]; }
    | T_EVAL '(' expr ')'                                   { $$ = Expr\Eval_[$3]; }
    | T_REQUIRE expr                                        { $$ = Expr\Include_[$2, Expr\Include_::TYPE_REQUIRE]; }
    | T_REQUIRE_ONCE expr                                   { $$ = Expr\Include_[$2, Expr\Include_::TYPE_REQUIRE_ONCE]; }
    | T_INT_CAST expr
          { $attrs = attributes();
            $attrs['kind'] = $this->getIntCastKind($1);
            $$ = new Expr\Cast\Int_($2, $attrs); }
    | T_DOUBLE_CAST expr
          { $attrs = attributes();
            $attrs['kind'] = $this->getFloatCastKind($1);
            $$ = new Expr\Cast\Double($2, $attrs); }
    | T_STRING_CAST expr
          { $attrs = attributes();
            $attrs['kind'] = $this->getStringCastKind($1);
            $$ = new Expr\Cast\String_($2, $attrs); }
    | T_ARRAY_CAST expr                                     { $$ = Expr\Cast\Array_  [$2]; }
    | T_OBJECT_CAST expr                                    { $$ = Expr\Cast\Object_ [$2]; }
    | T_BOOL_CAST expr
          { $attrs = attributes();
            $attrs['kind'] = $this->getBoolCastKind($1);
            $$ = new Expr\Cast\Bool_($2, $attrs); }
    | T_UNSET_CAST expr                                     { $$ = Expr\Cast\Unset_  [$2]; }
    | T_VOID_CAST expr                                      { $$ = Expr\Cast\Void_   [$2]; }
    | T_EXIT ctor_arguments
          { $$ = $this->createExitExpr($1, #1, $2, attributes()); }
    | '@' expr                                              { $$ = Expr\ErrorSuppress[$2]; }
    | scalar
    | '`' backticks_expr '`'                                { $$ = Expr\ShellExec[$2]; }
    | T_PRINT expr                                          { $$ = Expr\Print_[$2]; }
    | T_YIELD                                               { $$ = Expr\Yield_[null, null]; }
    | T_YIELD expr                                          { $$ = Expr\Yield_[$2, null]; }
    | T_YIELD expr T_DOUBLE_ARROW expr                      { $$ = Expr\Yield_[$4, $2]; }
    | T_YIELD_FROM expr                                     { $$ = Expr\YieldFrom[$2]; }
    | T_THROW expr                                          { $$ = Expr\Throw_[$2]; }

    | T_FN optional_ref '(' parameter_list ')' optional_return_type T_DOUBLE_ARROW expr %prec T_THROW
          { $$ = Expr\ArrowFunction[['static' => false, 'byRef' => $2, 'params' => $4, 'returnType' => $6, 'expr' => $8, 'attrGroups' => []]]; }
    | T_STATIC T_FN optional_ref '(' parameter_list ')' optional_return_type T_DOUBLE_ARROW expr %prec T_THROW
          { $$ = Expr\ArrowFunction[['static' => true, 'byRef' => $3, 'params' => $5, 'returnType' => $7, 'expr' => $9, 'attrGroups' => []]]; }
    | T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type block_or_error
          { $$ = Expr\Closure[['static' => false, 'byRef' => $2, 'params' => $4, 'uses' => $6, 'returnType' => $7, 'stmts' => $8, 'attrGroups' => []]]; }
    | T_STATIC T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type       block_or_error
          { $$ = Expr\Closure[['static' => true, 'byRef' => $3, 'params' => $5, 'uses' => $7, 'returnType' => $8, 'stmts' => $9, 'attrGroups' => []]]; }

    | attributes T_FN optional_ref '(' parameter_list ')' optional_return_type T_DOUBLE_ARROW expr %prec T_THROW
          { $$ = Expr\ArrowFunction[['static' => false, 'byRef' => $3, 'params' => $5, 'returnType' => $7, 'expr' => $9, 'attrGroups' => $1]]; }
    | attributes T_STATIC T_FN optional_ref '(' parameter_list ')' optional_return_type T_DOUBLE_ARROW expr %prec T_THROW
          { $$ = Expr\ArrowFunction[['static' => true, 'byRef' => $4, 'params' => $6, 'returnType' => $8, 'expr' => $10, 'attrGroups' => $1]]; }
    | attributes T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type block_or_error
          { $$ = Expr\Closure[['static' => false, 'byRef' => $3, 'params' => $5, 'uses' => $7, 'returnType' => $8, 'stmts' => $9, 'attrGroups' => $1]]; }
    | attributes T_STATIC T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type       block_or_error
          { $$ = Expr\Closure[['static' => true, 'byRef' => $4, 'params' => $6, 'uses' => $8, 'returnType' => $9, 'stmts' => $10, 'attrGroups' => $1]]; }
;

anonymous_class:
      optional_attributes class_entry_type ctor_arguments extends_from implements_list '{' class_statement_list '}'
          { $$ = array(Stmt\Class_[null, ['type' => $2, 'extends' => $4, 'implements' => $5, 'stmts' => $7, 'attrGroups' => $1]], $3);
            $this->checkClass($$[0], -1); }
;

new_dereferenceable:
      T_NEW class_name_reference argument_list              { $$ = Expr\New_[$2, $3]; }
    | T_NEW anonymous_class
          { list($class, $ctorArgs) = $2; $$ = Expr\New_[$class, $ctorArgs]; }
;

new_non_dereferenceable:
      T_NEW class_name_reference                            { $$ = Expr\New_[$2, []]; }
;

new_expr:
      new_dereferenceable
    | new_non_dereferenceable
;

lexical_vars:
      /* empty */                                           { $$ = array(); }
    | T_USE '(' lexical_var_list ')'                        { $$ = $3; }
;

lexical_var_list:
      non_empty_lexical_var_list optional_comma
;

non_empty_lexical_var_list:
      lexical_var                                           { init($1); }
    | non_empty_lexical_var_list ',' lexical_var            { push($1, $3); }
;

lexical_var:
      optional_ref plain_variable                           { $$ = Node\ClosureUse[$2, $1]; }
;

name_readonly:
      T_READONLY                                            { $$ = Name[$1]; }
;

function_call:
      name argument_list                                    { $$ = Expr\FuncCall[$1, $2]; }
    | name_readonly argument_list                           { $$ = Expr\FuncCall[$1, $2]; }
    | callable_expr argument_list                           { $$ = Expr\FuncCall[$1, $2]; }
    | class_name_or_var T_PAAMAYIM_NEKUDOTAYIM member_name argument_list
          { $$ = Expr\StaticCall[$1, $3, $4]; }
;

class_name:
      T_STATIC                                              { $$ = Name[$1]; }
    | name
;

name:
      T_STRING                                              { $$ = Name[$1]; }
    | T_NAME_QUALIFIED                                      { $$ = Name[$1]; }
    | T_NAME_FULLY_QUALIFIED                                { $$ = Name\FullyQualified[substr($1, 1)]; }
    | T_NAME_RELATIVE                                       { $$ = Name\Relative[substr($1, 10)]; }
;

class_name_reference:
      class_name
    | new_variable
    | '(' expr ')'                                          { $$ = $2; }
    | error                                                 { $$ = Expr\Error[]; $this->errorState = 2; }
;

class_name_or_var:
      class_name
    | fully_dereferenceable
;

backticks_expr:
      /* empty */                                           { $$ = array(); }
    | encaps_string_part
          { $$ = array($1); parseEncapsed($$, '`', $this->phpVersion->supportsUnicodeEscapes()); }
    | encaps_list                                           { parseEncapsed($1, '`', $this->phpVersion->supportsUnicodeEscapes()); $$ = $1; }
;

ctor_arguments:
      /* empty */                                           { $$ = array(); }
    | argument_list
;

constant:
      name                                                  { $$ = Expr\ConstFetch[$1]; }
    | T_LINE                                                { $$ = Scalar\MagicConst\Line[]; }
    | T_FILE                                                { $$ = Scalar\MagicConst\File[]; }
    | T_DIR                                                 { $$ = Scalar\MagicConst\Dir[]; }
    | T_CLASS_C                                             { $$ = Scalar\MagicConst\Class_[]; }
    | T_TRAIT_C                                             { $$ = Scalar\MagicConst\Trait_[]; }
    | T_METHOD_C                                            { $$ = Scalar\MagicConst\Method[]; }
    | T_FUNC_C                                              { $$ = Scalar\MagicConst\Function_[]; }
    | T_NS_C                                                { $$ = Scalar\MagicConst\Namespace_[]; }
    | T_PROPERTY_C                                          { $$ = Scalar\MagicConst\Property[]; }
;

class_constant:
      class_name_or_var T_PAAMAYIM_NEKUDOTAYIM identifier_maybe_reserved
          { $$ = Expr\ClassConstFetch[$1, $3]; }
    | class_name_or_var T_PAAMAYIM_NEKUDOTAYIM '{' expr '}'
          { $$ = Expr\ClassConstFetch[$1, $4]; }
    /* We interpret an isolated FOO:: as an unfinished class constant fetch. It could also be
       an unfinished static property fetch or unfinished scoped call. */
    | class_name_or_var T_PAAMAYIM_NEKUDOTAYIM error
          { $$ = Expr\ClassConstFetch[$1, new Expr\Error(stackAttributes(#3))]; $this->errorState = 2; }
;

array_short_syntax:
      '[' array_pair_list ']'
          { $attrs = attributes(); $attrs['kind'] = Expr\Array_::KIND_SHORT;
            $$ = new Expr\Array_($2, $attrs); }
;

dereferenceable_scalar:
      T_ARRAY '(' array_pair_list ')'
          { $attrs = attributes(); $attrs['kind'] = Expr\Array_::KIND_LONG;
            $$ = new Expr\Array_($3, $attrs);
            $this->createdArrays->offsetSet($$); }
    | array_short_syntax
	      { $$ = $1; $this->createdArrays->offsetSet($$); }
    | T_CONSTANT_ENCAPSED_STRING
          { $$ = Scalar\String_::fromString($1, attributes(), $this->phpVersion->supportsUnicodeEscapes()); }
    | '"' encaps_list '"'
          { $attrs = attributes(); $attrs['kind'] = Scalar\String_::KIND_DOUBLE_QUOTED;
            parseEncapsed($2, '"', $this->phpVersion->supportsUnicodeEscapes()); $$ = new Scalar\InterpolatedString($2, $attrs); }
;

scalar:
      T_LNUMBER
          { $$ = $this->parseLNumber($1, attributes(), $this->phpVersion->allowsInvalidOctals()); }
    | T_DNUMBER                                             { $$ = Scalar\Float_::fromString($1, attributes()); }
    | dereferenceable_scalar
    | constant
    | class_constant
    | T_START_HEREDOC T_ENCAPSED_AND_WHITESPACE T_END_HEREDOC
          { $$ = $this->parseDocString($1, $2, $3, attributes(), stackAttributes(#3), true); }
    | T_START_HEREDOC T_END_HEREDOC
          { $$ = $this->parseDocString($1, '', $2, attributes(), stackAttributes(#2), true); }
    | T_START_HEREDOC encaps_list T_END_HEREDOC
          { $$ = $this->parseDocString($1, $2, $3, attributes(), stackAttributes(#3), true); }
;

optional_expr:
      /* empty */                                           { $$ = null; }
    | expr
;

fully_dereferenceable:
      variable
    | '(' expr ')'                                          { $$ = $2; }
    | dereferenceable_scalar
    | class_constant
    | new_dereferenceable
;

array_object_dereferenceable:
      fully_dereferenceable
    | constant
;

callable_expr:
      callable_variable
    | '(' expr ')'                                          { $$ = $2; }
    | dereferenceable_scalar
    | new_dereferenceable
;

callable_variable:
      simple_variable
    | array_object_dereferenceable '[' optional_expr ']'     { $$ = Expr\ArrayDimFetch[$1, $3]; }
#if PHP7
    | array_object_dereferenceable '{' expr '}'              { $$ = Expr\ArrayDimFetch[$1, $3]; }
#endif
    | function_call
    | array_object_dereferenceable T_OBJECT_OPERATOR property_name argument_list
          { $$ = Expr\MethodCall[$1, $3, $4]; }
    | array_object_dereferenceable T_NULLSAFE_OBJECT_OPERATOR property_name argument_list
          { $$ = Expr\NullsafeMethodCall[$1, $3, $4]; }
;

optional_plain_variable:
      /* empty */                                           { $$ = null; }
    | plain_variable
;

variable:
      callable_variable
    | static_member
    | array_object_dereferenceable T_OBJECT_OPERATOR property_name
          { $$ = Expr\PropertyFetch[$1, $3]; }
    | array_object_dereferenceable T_NULLSAFE_OBJECT_OPERATOR property_name
          { $$ = Expr\NullsafePropertyFetch[$1, $3]; }
;

simple_variable:
      plain_variable
    | '$' '{' expr '}'                                      { $$ = Expr\Variable[$3]; }
    | '$' simple_variable                                   { $$ = Expr\Variable[$2]; }
    | '$' error                                             { $$ = Expr\Variable[Expr\Error[]]; $this->errorState = 2; }
;

static_member_prop_name:
      simple_variable
          { $var = $1->name; $$ = \is_string($var) ? Node\VarLikeIdentifier[$var] : $var; }
;

static_member:
      class_name_or_var T_PAAMAYIM_NEKUDOTAYIM static_member_prop_name
          { $$ = Expr\StaticPropertyFetch[$1, $3]; }
;

new_variable:
      simple_variable
    | new_variable '[' optional_expr ']'                    { $$ = Expr\ArrayDimFetch[$1, $3]; }
#if PHP7
    | new_variable '{' expr '}'                             { $$ = Expr\ArrayDimFetch[$1, $3]; }
#endif
    | new_variable T_OBJECT_OPERATOR property_name          { $$ = Expr\PropertyFetch[$1, $3]; }
    | new_variable T_NULLSAFE_OBJECT_OPERATOR property_name { $$ = Expr\NullsafePropertyFetch[$1, $3]; }
    | class_name T_PAAMAYIM_NEKUDOTAYIM static_member_prop_name
          { $$ = Expr\StaticPropertyFetch[$1, $3]; }
    | new_variable T_PAAMAYIM_NEKUDOTAYIM static_member_prop_name
          { $$ = Expr\StaticPropertyFetch[$1, $3]; }
;

member_name:
      identifier_maybe_reserved
    | '{' expr '}'                                          { $$ = $2; }
    | simple_variable
;

property_name:
      identifier_not_reserved
    | '{' expr '}'                                          { $$ = $2; }
    | simple_variable
    | error                                                 { $$ = Expr\Error[]; $this->errorState = 2; }
;

list_expr:
      T_LIST '(' inner_array_pair_list ')'
          { $$ = Expr\List_[$3]; $$->setAttribute('kind', Expr\List_::KIND_LIST);
            $this->postprocessList($$); }
;

array_pair_list:
      inner_array_pair_list
          { $$ = $1; $end = count($$)-1; if ($$[$end]->value instanceof Expr\Error) array_pop($$); }
;

comma_or_error:
      ','
    | error
          { /* do nothing -- prevent default action of $$=$1. See #551. */ }
;

inner_array_pair_list:
      inner_array_pair_list comma_or_error array_pair       { push($1, $3); }
    | array_pair                                            { init($1); }
;

array_pair:
      expr                                                  { $$ = Node\ArrayItem[$1, null, false]; }
    | ampersand variable                                    { $$ = Node\ArrayItem[$2, null, true]; }
    | list_expr                                             { $$ = Node\ArrayItem[$1, null, false]; }
    | expr T_DOUBLE_ARROW expr                              { $$ = Node\ArrayItem[$3, $1,   false]; }
    | expr T_DOUBLE_ARROW ampersand variable                { $$ = Node\ArrayItem[$4, $1,   true]; }
    | expr T_DOUBLE_ARROW list_expr                         { $$ = Node\ArrayItem[$3, $1,   false]; }
    | T_ELLIPSIS expr                                       { $$ = new Node\ArrayItem($2, null, false, attributes(), true); }
    | /* empty */
        { /* Create an Error node now to remember the position. We'll later either report an error,
             or convert this into a null element, depending on whether this is a creation or destructuring context. */
          $attrs = $this->createEmptyElemAttributes($this->tokenPos);
          $$ = new Node\ArrayItem(new Expr\Error($attrs), null, false, $attrs); }
;

encaps_list:
      encaps_list encaps_var                                { push($1, $2); }
    | encaps_list encaps_string_part                        { push($1, $2); }
    | encaps_var                                            { init($1); }
    | encaps_string_part encaps_var                         { init($1, $2); }
;

encaps_string_part:
      T_ENCAPSED_AND_WHITESPACE
          { $attrs = attributes(); $attrs['rawValue'] = $1; $$ = new Node\InterpolatedStringPart($1, $attrs); }
;

encaps_str_varname:
      T_STRING_VARNAME                                      { $$ = Expr\Variable[$1]; }
;

encaps_var:
      plain_variable
    | plain_variable '[' encaps_var_offset ']'              { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | plain_variable T_OBJECT_OPERATOR identifier_not_reserved
          { $$ = Expr\PropertyFetch[$1, $3]; }
    | plain_variable T_NULLSAFE_OBJECT_OPERATOR identifier_not_reserved
          { $$ = Expr\NullsafePropertyFetch[$1, $3]; }
    | T_DOLLAR_OPEN_CURLY_BRACES expr '}'                   { $$ = Expr\Variable[$2]; }
    | T_DOLLAR_OPEN_CURLY_BRACES T_STRING_VARNAME '}'       { $$ = Expr\Variable[$2]; }
    | T_DOLLAR_OPEN_CURLY_BRACES encaps_str_varname '[' expr ']' '}'
          { $$ = Expr\ArrayDimFetch[$2, $4]; }
    | T_CURLY_OPEN variable '}'                             { $$ = $2; }
;

encaps_var_offset:
      T_STRING                                              { $$ = Scalar\String_[$1]; }
    | T_NUM_STRING                                          { $$ = $this->parseNumString($1, attributes()); }
    | '-' T_NUM_STRING                                      { $$ = $this->parseNumString('-' . $2, attributes()); }
    | plain_variable
;

%%
