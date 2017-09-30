<?php

namespace PhpParser\Builder;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ParserBuilderFactory
{

    /**
     * @var string
     */
    private $binaryPath = 'kmyacc';

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var bool
     */
    private $keepTmpGrammar = false;

    /**
     * @var string
     */
    private $tokensTemplateFile;

    /**
     * @var string
     */
    private $parserTemplateFile;

    /**
     * @var string
     */
    private $tmpGrammarFile;

    /**
     * @var string
     */
    private $tmpResultFile;

    /**
     * @var string
     */
    private $resultDir;

    /**
     * @var string
     */
    private $tokensResultFile;

    private function __construct()
    {
    }

    /**
     * @return ParserBuilderFactory
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Returns a configured ParserBuilder instance
     * @return ParserBuilder
     * @throws \UnexpectedValueException
     */
    public function getParserBuilder(): ParserBuilder
    {
        if (null === $this->resultDir) {
            throw new \UnexpectedValueException('Please provide a result dir for the parser and token files');
        }

        return new ParserBuilder(
            $this->binaryPath,
            $this->debug,
            $this->keepTmpGrammar,
            $this->tmpGrammarFile,
            $this->tmpResultFile,
            $this->parserTemplateFile,
            $this->tokensTemplateFile,
            $this->resultDir,
            $this->tokensResultFile
        );
    }

    /**
     * Path to the kmyacc binary
     * @param string $binaryPath
     * @return self
     */
    public function setBinaryPath(string $binaryPath): self
    {
        $this->binaryPath = $binaryPath;
        return $this;
    }

    /**
     * Run kmyacc in debug mode
     * @param bool $debug
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Keep the temporary grammar file (@see self::setTmpGrammarFile)
     * @param bool $keepTmpGrammar
     * @return self
     */
    public function setKeepTmpGrammar(bool $keepTmpGrammar): self
    {
        $this->keepTmpGrammar = $keepTmpGrammar;
        return $this;
    }

    /**
     * Path to the tokens template file used by kmyacc
     * @param string $tokensTemplateFile
     * @return self
     */
    public function setTokensTemplateFile(string $tokensTemplateFile): self
    {
        $this->tokensTemplateFile = $tokensTemplateFile;
        return $this;
    }

    /**
     * Path to the parser template file used by kmyacc
     * @param string $parserTemplateFile
     * @return self
     */
    public function setParserTemplateFile(string $parserTemplateFile): self
    {
        $this->parserTemplateFile = $parserTemplateFile;
        return $this;
    }

    /**
     * Path for the preprocessed grammar file used by kmyacc as input
     * @param string $tmpGrammarFile
     * @return self
     */
    public function setTmpGrammarFile(string $tmpGrammarFile): self
    {
        $this->tmpGrammarFile = $tmpGrammarFile;
        return $this;
    }

    /**
     * @param string $tmpResultFile
     * @return self
     */
    public function setTmpResultFile(string $tmpResultFile): self
    {
        $this->tmpResultFile = $tmpResultFile;
        return $this;
    }

    /**
     * The result dir where the parser and tokens file should be stored in
     * @param string $resultDir
     * @return self
     */
    public function setResultDir(string $resultDir): self
    {
        $this->resultDir = $resultDir;
        return $this;
    }

    /**
     * Name of the tokens file
     * @param string $tokensResultFile
     * @return self
     */
    public function setTokensResultFile(string $tokensResultFile): self
    {
        $this->tokensResultFile = $tokensResultFile;
        return $this;
    }
}
