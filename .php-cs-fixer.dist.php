<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('PhpParser/Parser')
    ->in(__DIR__ . '/lib')
    ->in(__DIR__ . '/test')
    ->in(__DIR__ . '/grammar')
;

$config = new PhpCsFixer\Config();
return $config->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        // We use PSR12 with consistent brace placement.
        'curly_braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
        ],
        // declare(strict_types=1) on the same line as <?php.
        'blank_line_after_opening_tag' => false,
        'declare_strict_types' => true,
        // Keep argument formatting for now.
        'method_argument_space' => ['on_multiline' => 'ignore'],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_trim' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'no_extra_blank_lines' => true,
    ])
    ->setFinder($finder)
;
