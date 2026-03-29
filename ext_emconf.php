<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mai Environment',
    'description' => 'Helper extension for environment-dependent TYPO3 configuration. Loads `.env` files via `helhum/dotenv-connector` and provides utilities to detect the active environment (development, staging, production) and apply appropriate settings.',
    'category' => 'module',
    'author' => 'Maispace',
    'author_email' => '',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
