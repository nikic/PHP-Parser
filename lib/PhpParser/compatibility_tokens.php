<?php declare(strict_types=1);

namespace PhpParser;

if (!\function_exists('PhpParser\defineCompatibilityTokens')) {
    function defineCompatibilityTokens(): void {
        $compatTokens = [
            // PHP 7.4
            'T_BAD_CHARACTER',
            'T_FN',
            'T_COALESCE_EQUAL',
            // PHP 8.0
            'T_NAME_QUALIFIED',
            'T_NAME_FULLY_QUALIFIED',
            'T_NAME_RELATIVE',
            'T_MATCH',
            'T_NULLSAFE_OBJECT_OPERATOR',
            'T_ATTRIBUTE',
            // PHP 8.1
            'T_ENUM',
            'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
            'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
            'T_READONLY',
        ];

        // PHP-Parser might be used together with another library that also emulates some or all
        // of these tokens. Perform a sanity-check that all already defined tokens have been
        // assigned a unique ID.
        $usedTokenIds = [];
        foreach ($compatTokens as $token) {
            if (\defined($token)) {
                $tokenId = \constant($token);
                $clashingToken = $usedTokenIds[$tokenId] ?? null;
                if ($clashingToken !== null) {
                    throw new \Error(sprintf(
                        'Token %s has same ID as token %s, ' .
                        'you may be using a library with broken token emulation',
                        $token, $clashingToken
                    ));
                }
                $usedTokenIds[$tokenId] = $token;
            }
        }

        // Now define any tokens that have not yet been emulated. Try to assign IDs from -1
        // downwards, but skip any IDs that may already be in use.
        $newTokenId = -1;
        foreach ($compatTokens as $token) {
            if (!\defined($token)) {
                while (isset($usedTokenIds[$newTokenId])) {
                    $newTokenId--;
                }
                \define($token, $newTokenId);
                $newTokenId--;
            }
        }
    }

    defineCompatibilityTokens();
}
