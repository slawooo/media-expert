<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'global_namespace_import' => false,
        'single_line_throw' => false,
        'no_unneeded_control_parentheses' => false,
        'increment_style' => false,
        'yoda_style' => false,
        'nullable_type_declaration' => false,
        'concat_space' => false,

    ])
    ->setFinder($finder)
    ;
