<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default encryption cipher method
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption cipher method that gets used when encrypting
    | and decrypting strings. The method specified in this option should match
    | the method used by the application using pkt-starter-kit crypt helper.
    |
    */

    'cipher' => env('CRYPT_CHIPER', 'AES-256-CBC'),

    /*
    |--------------------------------------------------------------------------
    | Default encryption key
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption key that gets used when encrypting
    | and decrypting strings. The key specified in this option should match
    | the key used by the application using pkt-starter-kit crypt helper.
    |
    */

    'key' => env('CRYPT_KEY', env('APP_KEY')),

    'previous_key' => env('CRYPT_PREVIOUS_KEY', env('CRYPT_KEY', env('APP_KEY'))),

    /*
    |--------------------------------------------------------------------------
    | Default encryption iv
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption iv that gets used when encrypting and
    | decrypting strings. The iv specified should have 16 characters and match the
    | iv used by the application using pkt-starter-kit crypt helper.
    |
    */

    'iv' => env('CRYPT_IV', substr(explode(':', env('APP_KEY'))[1], 0, 16)),

    'previous_iv' => env('CRYPT_PREVIOUS_IV', env('CRYPT_IV', substr(explode(':', env('APP_KEY'))[1], 0, 16))),
    
    /*
    |--------------------------------------------------------------------------
    | Default encryption options
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption options that gets used when encrypting
    | and decrypting strings. The options specified in this option should match
    | the options used by the application using pkt-starter-kit crypt helper.
    |
    */

    'options' => 0,
];