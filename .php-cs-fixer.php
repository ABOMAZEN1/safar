<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/tests')
    ->exclude('database/migrations');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'       => true,
        'array_syntax' => ['syntax' => 'short'],
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'no_unused_imports' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'binary_operator_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'method_chaining_indentation' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'throw',
                'use',
                'use_trait',
            ],
        ],
        'declare_strict_types' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'void_return' => true,
        'strict_param' => true,
        'strict_comparison' => true,
        'fully_qualified_strict_types' => true,
        'no_leading_import_slash' => true,
        'no_unneeded_import_alias' => true,
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_order' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_scalar' => true,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'no_alternative_syntax' => true,
        'no_superfluous_elseif' => true,
        'simplified_if_return' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_trailing_comma_in_singleline' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => ['elements' => ['property']],
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'single_trait_insert_per_statement' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
