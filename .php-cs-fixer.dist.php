<?php

declare(strict_types=1);

use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->ignoreVCS(true)
    ->ignoreDotFiles(true)
    ->in(__DIR__)
    ->append([__FILE__])
;

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
$config->setUsingCache(true);
$config->registerCustomFixers(new Fixers());
$config->setFinder($finder);
$config->setRules(
    RuleSetFactory::create()
        ->php(8.3, true)
        ->phpCsFixer(true)
        ->pedrotroller(true)
        ->enable('PedroTroller/line_break_between_method_arguments', [
            'max-length' => 80,
        ])
        ->enable('align_multiline_comment')
        ->enable('array_indentation')
        ->enable('binary_operator_spaces', [
            'operators' => [
                '='  => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
            ],
        ])
        ->enable('class_attributes_separation', [
            'elements' => [
                'const'    => 'one',
                'method'   => 'one',
                'property' => 'one',
            ],
        ])
        ->enable('class_definition', [
            'single_line'                  => true,
            'inline_constructor_arguments' => false,
        ])
        ->enable('fully_qualified_strict_types')
        ->enable('linebreak_after_opening_tag')
        ->enable('mb_str_functions')
        ->enable('native_function_invocation')
        ->enable('no_extra_blank_lines', [
            'tokens' => [
                'break',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ])
        ->enable('no_superfluous_elseif')
        ->enable('no_superfluous_phpdoc_tags', [
            'allow_mixed' => true,
        ])
        ->enable('no_useless_else')
        ->enable('ordered_class_elements')
        ->enable('ordered_imports')
        ->enable('phpdoc_order')
        ->enable('concat_space', ['spacing' => 'one'])
        ->enable('blank_line_before_statement', [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'exit',
                'goto',
                'if',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'yield',
            ],
        ])
        ->enable('trailing_comma_in_multiline', [
            'after_heredoc' => true,
            'elements'      => ['arguments', 'arrays', 'match', 'parameters'],
        ])
        ->enable('global_namespace_import', [
            'import_classes'   => true,
            'import_constants' => false,
            'import_functions' => false,
        ])
        ->disable('PedroTroller/exceptions_punctuation')
        ->disable('phpdoc_add_missing_param_annotation')
        ->disable('phpdoc_to_comment')
        ->disable('return_assignment')
        ->disable('strict_comparison')
        ->getRules(),
);

return $config;
