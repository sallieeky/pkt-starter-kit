<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Enable SSO Feature
    |--------------------------------------------------------------------------
    |
    | When this option is set to true, the application will use SSO feature to authenticate user.
    | So the application will not use the internal login feature and will redirect user to
    | the portal login page before user can access the application.
    |
    */

    'ENABLE_SSO' => env('ENABLE_SSO', false),
    
    /*
    |--------------------------------------------------------------------------
    | Disable Internal Login
    |--------------------------------------------------------------------------
    |
    | When this option is set to true, the application will disable the internal login feature.
    | So the application will not use the internal login feature and will redirect user to
    | the portal login page before user can access the application.
    |
    */

    'DISABLE_INTERNAL_LOGIN' => env('DISABLE_INTERNAL_LOGIN', true),
    
    /*
    |--------------------------------------------------------------------------
    | SSO Full Feature
    |--------------------------------------------------------------------------
    |
    | When this option is set to true, the application will use full feature of SSO.
    | So the application will be able to access the portal API to get user data
    | and to check user permission to access the application.
    |
    */

    'SSO_FULL_FEATURE' => env('SSO_FULL_FEATURE', true),
    
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of Application Name to be known by the portal
    | The value should be match with the application name registered in the portal.
    | So the portal will know which application is trying to access the portal.
    |
    */

    'APPLICATION_NAME' => env('APPLICATION_NAME', 'pkt_starter_kit'),
    
    /*
    |--------------------------------------------------------------------------
    | Portal URL
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of Portal URL to be used when redirecting user
    | to the portal page when the user is not authorized to access the application.
    | The value should be match with the portal URL registered in the portal.
    |
    */

    'PORTAL_URL' => env('PORTAL_URL', 'https://aplikasi.pupukkaltim.com/'),
    
    /*
    |--------------------------------------------------------------------------
    | Portal URL Login
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of Portal URL Login to be used when redirecting user
    | to the portal login page when the user is not authenticated to access the application.
    | The value should be the portal login URL.
    |
    */

    'PORTAL_URL_LOGIN' => env('PORTAL_URL_LOGIN', 'https://aplikasi.pupukkaltim.com/login'),
    
    /*
    |--------------------------------------------------------------------------
    | Portal URL Logout
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of Portal URL Logout to be used when redirecting user
    | to the portal logout page when the user is logging out from the application.
    | The value should be the portal logout URL.
    |
    */

    'PORTAL_URL_LOGOUT' => env('PORTAL_URL_LOGOUT', 'https://aplikasi.pupukkaltim.com/logout'),
    
    /*
    |--------------------------------------------------------------------------
    | SSO Auth Token
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of SSO Auth Token to be used when accessing the portal API.
    | The auth token is used to authenticate the application to access the portal API.
    | The value should be match with the auth token registered in the portal.
    |
    */

    'SSO_AUTH_TOKEN' => env('SSO_AUTH_TOKEN', 'ASK ADMIN PORTAL'),
    
    /*
    |--------------------------------------------------------------------------
    | SSO API Key
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of SSO API Key to be used when accessing the portal API.
    | The API key is used to authenticate the application to access the portal API.
    | The value should be match with the API key registered in the portal.
    |
    */

    'API_KEY_PORTAL' => env('API_KEY_PORTAL', 'ASK ADMIN PORTAL'),
    
    /*
    |--------------------------------------------------------------------------
    | Authorization Token
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of Authorization Token to be used when accessing the portal API.
    | The authorization token is used to authenticate the application to access the portal API.
    | The value should be match with the authorization token registered in the portal.
    |
    */

    'AUTHORIZATION_TOKEN_PORTAL' => env('AUTHORIZATION_TOKEN_PORTAL', 'ASK ADMIN PORTAL')
];
