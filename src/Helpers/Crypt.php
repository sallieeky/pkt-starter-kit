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
        $method = $method ?? config('crypt.cipher');
        $options = config('crypt.options') ?? 0;
        $iv = $iv ?? config('crypt.iv');
        $key = $key ?? config('crypt.key');

        $string = is_array($string) || is_object($string) ? json_encode($string) : $string;
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
        $method = $method ?? config('crypt.cipher');
        $options = config('crypt.options') ?? 0;
        $iv = $iv ?? config('crypt.iv');
        $key = $key ?? config('crypt.key');

        $decryptedData = openssl_decrypt($string, $method, $key, $options, $iv);
        return $decryptedData;
    }
}
