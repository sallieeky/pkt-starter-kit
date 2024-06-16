<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Enable LDAP Feature
    |--------------------------------------------------------------------------
    |
    | This option defines the default value of LDAP feature that gets used when
    | authenticating user. The value specified in this option should return
    | boolean value to enable or disable LDAP feature.
    |
    */ 

    'LDAP_ENABLE' => env('LDAP_ENABLE', false),
    
    /*
    |--------------------------------------------------------------------------
    | Define LDAP Host
    |--------------------------------------------------------------------------
    |
    | This option defines the default host of LDAP server that gets used when
    | authenticating user. The value specified in this option should return
    | string value of LDAP server host.
    |
    */

    'LDAP_HOST' => env('LDAP_HOST'),
    
    /*
    |--------------------------------------------------------------------------
    | Define LDAP Port
    |--------------------------------------------------------------------------
    |
    | This option defines the default port of LDAP server that gets used when
    | authenticating user. The value specified in this option should return
    | the integer value of LDAP server port.
    |
    */

    'LDAP_PORT' => env('LDAP_PORT'),
    
    /*
    |--------------------------------------------------------------------------
    | Define LDAP DN
    |--------------------------------------------------------------------------
    |
    | This option defines the default DN of LDAP server that gets used when
    | authenticating user. The value specified in this option should return
    | string value of LDAP server DN.
    |
    */

    'LDAP_DN' => env('LDAP_DN'),
    
    /*
    |--------------------------------------------------------------------------
    | Define LDAP Password
    |--------------------------------------------------------------------------
    |
    | This option defines the default password of LDAP server that gets used when
    | authenticating user. The value specified in this option should return
    | string value of LDAP server password.
    |
    */

    'LDAP_PASS' => env('LDAP_PASS'),
    
    /*
    |--------------------------------------------------------------------------
    | Define LDAP Tree
    |--------------------------------------------------------------------------
    |
    | This option defines the default tree of LDAP server that gets used when
    | authenticating user. The value specified in this option should return
    | string value of LDAP server tree.
    |
    */

    'LDAP_TREE' => env('LDAP_TREE'),
];