<?php
return [
    'payment_gateways' => [
        'okpay' => [
            'wallet_id' => getenv('OKPAY_WALLET_ID'),
            'api_password' => getenv('OKPAY_API_PASSWORD'),
        ],
        'perfectmoney' => [
            'account_id' => getenv('PM_ACCOUNT_ID'),
            'pass_phrase' => getenv('PM_PASS_PHRASE'),
            'alternate_pass_phrase' => getenv('PM_ALT_PASS_PHRASE'),
        ],
        'yandexmoney' => [
            'access_token' => getenv('YM_ACCESS_TOKEN'),
        ],
    ],
];
