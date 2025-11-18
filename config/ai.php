<?php
// config/ai.php

return [
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'models' => ['gpt-4', 'gpt-3.5-turbo'],
        ],
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
            'models' => ['deepseek-chat', 'deepseek-coder'],
        ],
    ],

    'costs' => [
        'openai' => [
            'gpt-4' => 0.03 / 1000, // per token
            'gpt-3.5-turbo' => 0.002 / 1000,
        ],
        'deepseek' => [
            'deepseek-chat' => 0.0014 / 1000,
            'deepseek-coder' => 0.0014 / 1000,
        ],
    ],
];
