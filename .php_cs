<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'protected_to_private' => false,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_separation' => false,
        'yoda_style' => null,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
            ->append([__FILE__])
    );
