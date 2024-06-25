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
    | Encryption key
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption key that gets used when encrypting
    | and decrypting strings. The key specified in this option should match
    | the key used by the application using pkt-starter-kit crypt helper.
    |
    */

    'key' => env('CRYPT_KEY', null),

    'previous_key' => env('CRYPT_PREVIOUS_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Encryption iv
    |--------------------------------------------------------------------------
    |
    | This option defines the default encryption iv that gets used when encrypting and
    | decrypting strings. The iv specified should have 16 characters and match the
    | iv used by the application using pkt-starter-kit crypt helper.
    |
    */

    'iv' => env('CRYPT_IV', null),

    'previous_iv' => env('CRYPT_PREVIOUS_IV', null),

    /*
    |--------------------------------------------------------------------------
    | Enable regenerating encryption key and iv
    |--------------------------------------------------------------------------
    |
    | This option defines whether the encryption key and iv can be regenerated
    | or not. If set to true, the encryption key and iv can be regenerated.
    | If set to false, the encryption key and iv cannot be regenerated.
    |
    */

    'regenerate' => env('CRYPT_REGENERATE', false),
    
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