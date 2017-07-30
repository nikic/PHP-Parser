<?php

namespace PhpParser\Builder;

class ParserBuilder
{

    const LIB = '(?(DEFINE)
        (?<singleQuotedString>\'[^\\\\\']*+(?:\\\\.[^\\\\\']*+)*+\')
        (?<doubleQuotedString>"[^\\\\"]*+(?:\\\\.[^\\\\"]*+)*+")
        (?<string>(?&singleQuotedString)|(?&doubleQuotedString))
        (?<comment>/\*[^*]*+(?:\*(?!/)[^*]*+)*+\*/)
        (?<code>\{[^\'"/{}]*+(?:(?:(?&string)|(?&comment)|(?&code)|/)[^\'"/{}]*+)*+})
    )';

    const PARAMS = '\[(?<params>[^[\]]*+(?:\[(?&params)\][^[\]]*+)*+)\]';
    const ARGS   = '\((?<args>[^()]*+(?:\((?&args)\)[^()]*+)*+)\)';

    /**
     * @var string
     */
    protected $binaryPath;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $keepTmpGrammar;

    /**
     * @var string
     */
    protected $parserTemplateFile;

    /**
     * @var string
     */
    protected $tokensTemplateFile;

    /**
     * @var string
     */
    protected $tmpGrammarFile;

    /**
     * @var string
     */
    protected $tmpResultFile;

    /**
     * @var string
     */
    protected $resultDir;

    /**
     * @var string
     */
    protected $tokensResultFile;

    /**
     * ParserBuilder constructor.
     * @param string $binaryPath
     * @param bool $debug Catch debug output of kmyacc
     * @param bool $keepTmpGrammar Keep temporary grammar file
     * @param string $tmpGrammarFile
     * @param string $tmpResultFile
     * @param string $parserTemplateFile
     * @param string $tokensTemplateFile
     * @param string $resultDir
     * @param string $tokensResultFile
     */
    public function __construct(
        string $binaryPath,
        bool $debug,
        bool $keepTmpGrammar,
        string $tmpGrammarFile,
        string $tmpResultFile,
        string $parserTemplateFile,
        string $tokensTemplateFile,
        string $resultDir,
        string $tokensResultFile
    ) {
        $this->binaryPath = $binaryPath;
        $this->debug = $debug;
        $this->keepTmpGrammar = $keepTmpGrammar;
        $this->tmpGrammarFile = $tmpGrammarFile;
        $this->tmpResultFile  = $tmpResultFile;
        $this->parserTemplateFile = $parserTemplateFile;
        $this->tokensTemplateFile = $tokensTemplateFile;
        $this->resultDir = $resultDir;
        $this->tokensResultFile = $tokensResultFile;
    }

    /**
     * @param string $parserName Name of the parser class
     * @param string $grammarFile Yacc grammar file used for parser
     * @param string|null $tokensFile Optional token file which will be merged into the grammar file
     */
    public function build(string $parserName, string $grammarFile, string $tokensFile = null)
    {
        echo "Building temporary $parserName grammar file.\n";

        $tokens = file_get_contents($tokensFile);
        $grammarCode = file_get_contents($grammarFile);
        $grammarCode = str_replace('%tokens', $tokens, $grammarCode);

        $grammarCode = $this->resolveNodes($grammarCode);
        $grammarCode = $this->resolveMacros($grammarCode);
        $grammarCode = $this->resolveStackAccess($grammarCode);

        file_put_contents($this->tmpGrammarFile, $grammarCode);

        $additionalArgs = $this->debug ? '-t -v' : '';

        echo "Building $parserName parser.\n";
        $output = trim(
            shell_exec(
                "$this->binaryPath $additionalArgs -l -m $this->parserTemplateFile -p $parserName $this->tmpGrammarFile 2>&1"
            )
        );
        echo "Output: \"$output\"\n";

        $resultCode = file_get_contents($this->tmpResultFile);
        $resultCode = $this->removeTrailingWhitespace($resultCode);

        $this->ensureDirExists($this->resultDir);
        file_put_contents($this->resultDir.DIRECTORY_SEPARATOR.$parserName.'.php', $resultCode);
        unlink($this->tmpResultFile);

        echo "Building token definition.\n";
        $output = trim(shell_exec("$this->binaryPath -l -m $this->tokensTemplateFile $this->tmpGrammarFile 2>&1"));
        assert($output === '');
        rename($this->tmpResultFile, $this->resultDir.DIRECTORY_SEPARATOR.$this->tokensResultFile);

        if (!$this->keepTmpGrammar) {
            unlink($this->tmpGrammarFile);
        }
    }

    /**
     * @param mixed $code
     * @return mixed
     */
    protected function resolveNodes($code)
    {
        return preg_replace_callback(
            '~(?<![\w])(?<name>\\\\?[A-Z][a-zA-Z_\\\\]++)\s*' . self::PARAMS . '~',
            function($matches) {
                // recurse
                $matches['params'] = $this->resolveNodes($matches['params']);

                $params = $this->magicSplit(
                    '(?:' . self::PARAMS . '|' . self::ARGS . ')(*SKIP)(*FAIL)|,',
                    $matches['params']
                );

                $paramCode = '';
                foreach ($params as $param) {
                    $paramCode .= $param . ', ';
                }

                return 'new ' . $matches['name'] . '(' . $paramCode . 'attributes())';
            },
            $code
        );
    }

    /**
     * @param mixed $code
     * @return mixed
     */
    protected function resolveMacros($code)
    {
        return preg_replace_callback(
            '~\b(?<!::|->)(?!array\()(?<name>[a-z][A-Za-z]++)' . self::ARGS . '~',
            function($matches) {
                // recurse
                $matches['args'] = $this->resolveMacros($matches['args']);

                $name = $matches['name'];
                $args = $this->magicSplit(
                    '(?:' . self::PARAMS . '|' . self::ARGS . ')(*SKIP)(*FAIL)|,',
                    $matches['args']
                );

                if ('attributes' == $name) {
                    $this->assertArgs(0, $args, $name);
                    return '$this->startAttributeStack[#1] + $this->endAttributes';
                }

                if ('stackAttributes' == $name) {
                    $this->assertArgs(1, $args, $name);
                    return '$this->startAttributeStack[' . $args[0] . ']'
                        . ' + $this->endAttributeStack[' . $args[0] . ']';
                }

                if ('init' == $name) {
                    return '$$ = array(' . implode(', ', $args) . ')';
                }

                if ('push' == $name) {
                    $this->assertArgs(2, $args, $name);
                    return $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0];
                }

                if ('pushNormalizing' == $name) {
                    $this->assertArgs(2, $args, $name);
                    return 'if (is_array(' . $args[1] . ')) { $$ = array_merge(' . $args[0] . ', ' . $args[1] . '); }'
                        . ' else { ' . $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0] . '; }';
                }

                if ('toArray' == $name) {
                    $this->assertArgs(1, $args, $name);
                    return 'is_array(' . $args[0] . ') ? ' . $args[0] . ' : array(' . $args[0] . ')';
                }

                if ('parseVar' == $name) {
                    $this->assertArgs(1, $args, $name);
                    return 'substr(' . $args[0] . ', 1)';
                }

                if ('parseEncapsed' == $name) {
                    $this->assertArgs(3, $args, $name);
                    return 'foreach (' . $args[0] . ' as $s) { if ($s instanceof Node\Scalar\EncapsedStringPart) {'
                        . ' $s->value = Node\Scalar\String_::parseEscapeSequences($s->value, ' . $args[1] . ', ' . $args[2] . '); } }';
                }

                if ('parseEncapsedDoc' == $name) {
                    $this->assertArgs(2, $args, $name);
                    return 'foreach (' . $args[0] . ' as $s) { if ($s instanceof Node\Scalar\EncapsedStringPart) {'
                        . ' $s->value = Node\Scalar\String_::parseEscapeSequences($s->value, null, ' . $args[1] . '); } }'
                        . ' $s->value = preg_replace(\'~(\r\n|\n|\r)\z~\', \'\', $s->value);'
                        . ' if (\'\' === $s->value) array_pop(' . $args[0] . ');';
                }

                if ('makeNop' == $name) {
                    $this->assertArgs(3, $args, $name);
                    return '$startAttributes = ' . $args[1] . ';'
                        . ' if (isset($startAttributes[\'comments\']))'
                        . ' { ' . $args[0] . ' = new Stmt\Nop($startAttributes + ' . $args[2] . '); }'
                        . ' else { ' . $args[0] . ' = null; }';
                }

                if ('strKind' == $name) {
                    $this->assertArgs(1, $args, $name);
                    return '(' . $args[0] . '[0] === "\'" || (' . $args[0] . '[1] === "\'" && '
                        . '(' . $args[0] . '[0] === \'b\' || ' . $args[0] . '[0] === \'B\')) '
                        . '? Scalar\String_::KIND_SINGLE_QUOTED : Scalar\String_::KIND_DOUBLE_QUOTED)';
                }

                if ('setDocStringAttrs' == $name) {
                    $this->assertArgs(2, $args, $name);
                    return $args[0] . '[\'kind\'] = strpos(' . $args[1] . ', "\'") === false '
                        . '? Scalar\String_::KIND_HEREDOC : Scalar\String_::KIND_NOWDOC; '
                        . 'preg_match(\'/\A[bB]?<<<[ \t]*[\\\'"]?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\\\'"]?(?:\r\n|\n|\r)\z/\', ' . $args[1] . ', $matches); '
                        . $args[0] . '[\'docLabel\'] = $matches[1];';
                }

                if ('prependLeadingComments' == $name) {
                    $this->assertArgs(1, $args, $name);
                    return '$attrs = $this->startAttributeStack[#1]; $stmts = ' . $args[0] . '; '
                        . 'if (!empty($attrs[\'comments\']) && isset($stmts[0])) {'
                        . '$stmts[0]->setAttribute(\'comments\', '
                        . 'array_merge($attrs[\'comments\'], $stmts[0]->getAttribute(\'comments\', []))); }';
                }

                return $matches[0];
            },
            $code
        );
    }

    /**
     * @param int $num
     * @param array $args
     * @param string $name
     */
    protected function assertArgs(int $num, array $args, string $name)
    {
        if ($num != count($args)) {
            die('Wrong argument count for ' . $name . '().');
        }
    }

    protected function resolveStackAccess($code)
    {
        $code = preg_replace('/\$\d+/', '$this->semStack[$0]', $code);
        $code = preg_replace('/#(\d+)/', '$$1', $code);
        return $code;
    }

    protected function removeTrailingWhitespace($code)
    {
        $lines = explode("\n", $code);
        $lines = array_map('rtrim', $lines);
        return implode("\n", $lines);
    }

    protected function ensureDirExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    //////////////////////////////
    /// Regex helper functions ///
    //////////////////////////////
    protected function regex($regex)
    {
        return '~' . self::LIB . '(?:' . str_replace('~', '\~', $regex) . ')~';
    }

    protected function magicSplit($regex, $string)
    {
        $pieces = preg_split($this->regex('(?:(?&string)|(?&comment)|(?&code))(*SKIP)(*FAIL)|' . $regex), $string);

        foreach ($pieces as &$piece) {
            $piece = trim($piece);
        }

        if ($pieces === ['']) {
            return [];
        }

        return $pieces;
    }
}
