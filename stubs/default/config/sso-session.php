<?php

return [
    'ENABLE_SSO' => env('ENABLE_SSO', false),
    'DISABLE_INTERNAL_LOGIN' => env('DISABLE_INTERNAL_LOGIN', true),
    'SSO_FULL_FEATURE' => env('SSO_FULL_FEATURE', true),
    'APPLICATION_NAME' => env('APPLICATION_NAME', 'pkt_starter_kit'),
    'PORTAL_URL' => env('PORTAL_URL', 'https://aplikasi.pupukkaltim.com/'),
    'PORTAL_URL_LOGIN' => env('PORTAL_URL_LOGIN', 'https://aplikasi.pupukkaltim.com/login'),
    'PORTAL_URL_LOGOUT' => env('PORTAL_URL_LOGOUT', 'https://aplikasi.pupukkaltim.com/logout'),
    'SSO_AUTH_TOKEN' => env('SSO_AUTH_TOKEN', 'ASK ADMIN PORTAL'),

    'API_KEY_PORTAL' => env('API_KEY_PORTAL', 'ASK ADMIN PORTAL'),
    'AUTHORIZATION_TOKEN_PORTAL' => env('AUTHORIZATION_TOKEN_PORTAL', 'ASK ADMIN PORTAL')
];
