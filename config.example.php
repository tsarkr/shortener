<?php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'your_database',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8mb4'
    ],
    'ads' => [
        'kakao' => [
            'main' => [
                'unit' => 'YOUR-MAIN-AD-UNIT',
                'width' => 320,
                'height' => 100
            ],
            'about' => [
                'unit' => 'YOUR-ABOUT-AD-UNIT',
                'width' => 320,
                'height' => 100
            ],
            'result' => [
                'unit' => 'YOUR-RESULT-AD-UNIT',
                'width' => 300,
                'height' => 250
            ]
        ]
    ],
    'analytics' => [
        'google' => [
            'enabled' => false,
            'id' => 'YOUR-GA4-ID'
        ]
    ]
];