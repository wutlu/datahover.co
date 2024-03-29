<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'recaptcha' => [
        'key' => env('RECAPTCHA_KEY'),
        'secret' => env('RECAPTCHA_SECRET')
    ],

    'google_analytics' => [
        'code' => env('GOOGLE_ANALYTICS_CODE')
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT', 'http://localhost/gate/callback'),
    ],

    'twitter' => [
        'consumer_apis' => [
            'Ipad' => [
                'consumer_key' => 'CjulERsDeqhhjSme66ECg',
                'consumer_secret' => 'IQWdVyqFxghAtURHGeGiWAsmCAGmdW3WmbEx6Hck'
            ],
            'Web' => [
                'consumer_key' => '3rJOl1ODzm9yZy63FACdg',
                'consumer_secret' => '5jPoQ5kQvMJFDYRNE8bQ4rHuds4xJqhvgNJM4awaE8'
            ],
            'Android' => [
                'consumer_key' => '3nVuSoBZnx6U4vzUxf5w',
                'consumer_secret' => 'Bcs59EFbbsdF6Sl9Ng71smgStWEGwXXKSjYvPVt7qys'
            ],
            'Iphone' => [
                'consumer_key' => 'IQKbtAYlXLripLGPWd0HUA',
                'consumer_secret' => 'GgDYlkSvaPxGxC4X8liwpUoqKwwr3lCADbz8A7ADU'
            ]
        ]
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'jivo' => [
        'code' => env('JIVO_CODE'),
    ],

    'datahover' => [
        'api_key' => env('DATAHOVER_API_KEY'),
        'api_secret' => env('DATAHOVER_API_SECRET'),
        'base_uri' => env('DATAHOVER_BASE_URI', 'https://datahover.co/api/v1'),

        'feeds' => [
            'twitter' => [
                'json' => 'https://datahover.co/storage/feeds/Vw4030655818220127104209SF/file.json',
                'xml' => 'https://datahover.co/storage/feeds/Vw4030655818220127104209SF/file.xml',
            ],
            'youtube' => [
                'json' => 'https://datahover.co/storage/feeds/om40306558gB220126110450of/file.json',
                'xml' => 'https://datahover.co/storage/feeds/om40306558gB220126110450of/file.xml',
            ],
            // 'instagram' => [
            //     'json' => 'https://datahover.co/storage/feeds/Bw40306558HD220126110428yw/file.json',
            //     'xml' => 'https://datahover.co/storage/feeds/Bw40306558HD220126110428yw/file.xml',
            // ],
            'news' => [
                'json' => 'https://datahover.co/storage/feeds/tz40306558hU220126110416Mg/file.json',
                'xml' => 'https://datahover.co/storage/feeds/tz40306558hU220126110416Mg/file.xml',
            ]
        ],
    ],

    'vimeo' => [
        'tutorials' => [
            'instagram_hashtag_tracking' => 'https://player.vimeo.com/video/671343835?h=16f764d237&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479',
            'twitter_keyword_tracking' => 'https://player.vimeo.com/video/671343798?h=ff0205a42d&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479',
            'youtube_keyword_tracking' => 'https://player.vimeo.com/video/671343862?h=b0d3633366&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479',
            'news_domain_tracking' => 'https://player.vimeo.com/video/671343852?h=ff753ae1c4&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479',
            'search_api' => 'https://player.vimeo.com/video/671343809?h=22280187c6&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479',
        ]
    ],

    'smartlook' => [
        'project_key' => env('SMARTLOOK_PROJECT_KEY'),
    ]
];
