<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\Error;
use PhpParser\ErrorHandler;

class Emulative extends \PhpParser\Lexer
{
    const PHP_7_3 = '7.3.0dev';

    /**
     * @var array Patches used to reverse changes introduced in the code
     */
    private $patches;

    public function startLexing(string $code, ErrorHandler $errorHandler = null) {
        $this->patches = [];
        $preparedCode = $this->prepareCode($code);
        if (null === $preparedCode) {
            // Nothing to emulate, yay
            parent::startLexing($code, $errorHandler);
            return;
        }

        $collector = new ErrorHandler\Collecting();
        parent::startLexing($preparedCode, $collector);
        $this->fixupTokens();

        $errors = $collector->getErrors();
        if (!empty($errors)) {
            $this->fixupErrors($errors);
            foreach ($errors as $error) {
                $errorHandler->handleError($error);
            }
        }
    }

    /**
     * Prepares code for emulation. If nothing has to be emulated null is returned.
     *
     * @param string $code
     * @return null|string
     */
    private function prepareCode(string $code) {
        if (version_compare(\PHP_VERSION, self::PHP_7_3, '>=')) {
            return null;
        }

        if (strpos($code, '<<<') === false) {
            // Definitely doesn't contain heredoc/nowdoc
            return null;
        }

        $flexibleDocStringRegex = <<<'REGEX'
/<<<[ \t]*(['"]?)([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)\1\r?\n
(?:.*\r?\n)*?
(?<indentation>\h*)\2(?![a-zA-Z_\x80-\xff])(?<separator>(?:;?[\r\n])?)/x
REGEX;
        if (!preg_match_all($flexibleDocStringRegex, $code, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE)) {
            // No heredoc/nowdoc found
            return null;
        }

        // Keep track of how much we need to adjust string offsets due to the modifications we
        // already made
        $posDelta = 0;
        foreach ($matches as $match) {
            $indentation = $match['indentation'][0];
            $indentationStart = $match['indentation'][1];

            $separator = $match['separator'][0];
            $separatorStart = $match['separator'][1];

            if ($indentation === '' && $separator !== '') {
                // Ordinary heredoc/nowdoc
                continue;
            }

            if ($indentation !== '') {
                // Remove indentation
                $indentationLen = strlen($indentation);
                $code = substr_replace($code, '', $indentationStart + $posDelta, $indentationLen);
                $this->patches[] = [$indentationStart + $posDelta, 'add', $indentation];
                $posDelta -= $indentationLen;
            }

            if ($separator === '') {
                // Insert newline as separator
                $code = substr_replace($code, "\n", $separatorStart + $posDelta, 0);
                $this->patches[] = [$separatorStart + $posDelta, 'remove', "\n"];
                $posDelta += 1;
            }
        }

        if (empty($this->patches)) {
            // We did not end up emulating anything
            return null;
        }

        return $code;
    }

    private function fixupTokens() {
        assert(count($this->patches) > 0);

        // Load first patch
        $patchIdx = 0;
        list($patchPos, $patchType, $patchText) = $this->patches[$patchIdx];

        // We use a manual loop over the tokens, because we modify the array on the fly
        $pos = 0;
        for ($i = 0, $c = \count($this->tokens); $i < $c; $i++) {
            $token = $this->tokens[$i];
            if (\is_string($token)) {
                // We assume that patches don't apply to string tokens
                $pos += \strlen($token);
                continue;
            }

            $len = \strlen($token[1]);
            $posDelta = 0;
            while ($patchPos >= $pos && $patchPos < $pos + $len) {
                $patchTextLen = \strlen($patchText);
                if ($patchType === 'remove') {
                    if ($patchPos === $pos && $patchTextLen === $len) {
                        // Remove token entirely
                        array_splice($this->tokens, $i, 1, []);
                        $i--;
                        $c--;
                    } else {
                        // Remove from token string
                        $this->tokens[$i][1] = substr_replace(
                            $token[1], '', $patchPos - $pos + $posDelta, $patchTextLen
                        );
                        $posDelta -= $patchTextLen;
                    }
                } elseif ($patchType === 'add') {
                    // Insert into the token string
                    $this->tokens[$i][1] = substr_replace(
                        $token[1], $patchText, $patchPos - $pos + $posDelta, 0
                    );
                    $posDelta += $patchTextLen;
                } else {
                    assert(false);
                }

                // Fetch the next patch
                $patchIdx++;
                if ($patchIdx >= \count($this->patches)) {
                    // No more patches, we're done
                    return;
                }

                list($patchPos, $patchType, $patchText) = $this->patches[$patchIdx];

                // Multiple patches may apply to the same token. Reload the current one to check
                // If the new patch applies
                $token = $this->tokens[$i];
            }

            $pos += $len;
        }

        // A patch did not apply
        assert(false);
    }

    /**
     * Fixup line and position information in errors.
     *
     * @param Error[] $errors
     */
    private function fixupErrors(array $errors) {
        foreach ($errors as $error) {
            $attrs = $error->getAttributes();

            $posDelta = 0;
            $lineDelta = 0;
            foreach ($this->patches as $patch) {
                list($patchPos, $patchType, $patchText) = $patch;
                if ($patchPos >= $attrs['startFilePos']) {
                    // No longer relevant
                    break;
                }

                if ($patchType === 'add') {
                    $posDelta += strlen($patchText);
                    $lineDelta += substr_count($patchText, "\n");
                } else {
                    $posDelta -= strlen($patchText);
                    $lineDelta -= substr_count($patchText, "\n");
                }
            }

            $attrs['startFilePos'] += $posDelta;
            $attrs['endFilePos'] += $posDelta;
            $attrs['startLine'] += $lineDelta;
            $attrs['endLine'] += $lineDelta;
            $error->setAttributes($attrs);
        }
    }
}