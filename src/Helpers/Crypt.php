<?php

namespace Pkt\StarterKit\Helpers;

class Crypt
{
    /**
     * Encrypt the given string.
     *
     * @param string $string
     * @return string
     */
    public static function encrypt($string): string
    {
        $method = "AES-256-CBC";
        $options = 0;
        $iv = substr(config('app.key'), 0, 16);
        $key = config('app.key');

        $encryptedData = openssl_encrypt($string, $method, $key, $options, $iv);
        return $encryptedData;
    }

    /**
     * Decrypt the given string.
     *
     * @param string $string
     * @return string
     */
    public static function decrypt($string): string
    {
        $method = "AES-256-CBC";
        $options = 0;
        $iv = substr(config('app.key'), 0, 16);
        $key = config('app.key');

        $decryptedData = openssl_decrypt($string, $method, $key, $options, $iv);
        return $decryptedData;
    }
}
