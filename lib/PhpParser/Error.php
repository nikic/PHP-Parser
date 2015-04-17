<?php

namespace PhpParser;

class Error extends \RuntimeException
{
    protected $rawMessage;
    protected $rawLine;
    protected $tokens = array();
    protected $tokenIndex;
    protected $beginColumnCache;

    /**
     * Creates an Exception signifying a parse error.
     *
     * @param string $message    Error message
     * @param int    $line       Error line in PHP file
     * @param array  $tokens     Array of all tokens in the file that caused the error.
     * @param int    $tokenIndex Index in $tokens of token where error happened.
     */
    public function __construct($message, $line = -1, array $tokens=array(), $tokenIndex=null) {
        $this->rawMessage = (string) $message;
        $this->rawLine    = (int) $line;
        $this->tokens     = $tokens;
        $this->tokenIndex = $tokenIndex;
        $this->updateMessage();
    }

    /**
     * Gets the error message
     *
     * @return string Error message
     */
    public function getRawMessage() {
        return $this->rawMessage;
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param string $message Error message
     */
    public function setRawMessage($message) {
        $this->rawMessage = (string) $message;
        $this->updateMessage();
    }

    /**
     * Gets the error line in the PHP file.
     *
     * @return int Error line in the PHP file
     */
    public function getRawLine() {
        return $this->rawLine;
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param int $line Error line in the PHP file
     */
    public function setRawLine($line) {
        $this->rawLine = (int) $line;
        $this->updateMessage();
    }

    /**
     * Checks if valid token-information is available for this error.
     * 
     * @return bool
     */
    public function hasTokenAttributes(){
        return is_numeric($this->tokenIndex) && isset($this->tokens[(int)$this->tokenIndex]);
    }

    /**
     * Gets the tokens for the php-file in which this error happened.
     * Only works if token-information was provided.
     * 
     * @return array
     */
    public function getTokens(){
        return $this->tokens;
    }

    /**
     * Get sthe index in tokens in which this error happened.
     * Only works if token-information was provided.
     * 
     * @return int
     */
    public function getTokenIndex(){
        return $this->tokenIndex;
    }

    /**
     * Gets the first column number in which the error happened.
     * Only works if token-information was provided.
     * 
     * @return int
     */
    public function getBeginColumn() {
        $beginColumn = null;

        if($this->hasTokenAttributes()) {
        
            if(!is_null($this->beginColumnCache)) {
                $beginColumn = $this->beginColumnCache;

            } else {
                $beginColumn = 0;
                $tokenIndex = $this->tokenIndex;
                for($i=$tokenIndex-1;$i>=0;$i--) {
                    $tokenText = $this->getTextFromToken($this->tokens[$i]);

                    $beginColumn += strlen($tokenText);

                    $newlinePosition = strrpos($tokenText, "\n");
                    if($newlinePosition !== false){
                        $beginColumn -= $newlinePosition;
                        break;
                    }
                }
            }

        }

        return $beginColumn;
    }

    /**
     * Gets the last column number in which the error happened.
     * Only works if token-information was provided.
     * 
     * @return int
     */
    public function getEndColumn(){
        $endColumn = null;

        if($this->hasTokenAttributes()){
            $beginColumn = $this->getBeginColumn();
            $token       = $this->tokens[(int)$this->tokenIndex];
            $tokenText   = $this->getTextFromToken($token);
            $endColumn   = $beginColumn + strlen($tokenText);
        }

        return $endColumn;
    }

    private function getTextFromToken($token){

        $tokenText = $token;
        if(is_array($tokenText)){
            if(is_int($tokenText[0])){
                $tokenText = $tokenText[1];

            }else{
                $tokenText = $tokenText[0];
            }
        }

        return $tokenText;
    }

    /**
     * Updates the exception message after a change to rawMessage or rawLine.
     */
    protected function updateMessage() {
        $this->message = $this->rawMessage;

        if (-1 === $this->rawLine) {
            $this->message .= ' on unknown line';
        } else {
            $this->message .= ' on line ' . $this->rawLine;
        }

    }
}
