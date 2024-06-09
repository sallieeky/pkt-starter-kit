<?php

namespace Pkt\StarterKit\Helpers;

class Crypt
{
    /**
     * Encrypt the given string.
     *
     * @param string|integer $string
     * @param ?string $method
     * @param ?string $iv
     * @param ?string $key
     * 
     * @return string
     */
    public static function encrypt($string, string $method = null, string $iv = null, string $key = null): string
    {
        $method = $method ?? "AES-256-CBC";
        $options = 0;
        $iv = $iv ?? substr(explode(':', config('app.key'))[1], 0, 16);
        $key = $key ?? config('app.key');

        $encryptedData = openssl_encrypt($string, $method, $key, $options, $iv);
        return $encryptedData;
    }

    /**
     * Decrypt the given string.
     *
     * @param string|integer $string
     * @param ?string $method
     * @param ?string $iv
     * @param ?string $key
     *
     * @return string
     */
    public static function decrypt($string, string $method = null, string $iv = null, string $key = null): string
    {
        $method = $method ?? "AES-256-CBC";
        $options = 0;
        $iv = $iv ?? substr(explode(':', config('app.key'))[1], 0, 16);
        $key = $key ?? config('app.key');

        $decryptedData = openssl_decrypt($string, $method, $key, $options, $iv);
        return $decryptedData;
    }
}
