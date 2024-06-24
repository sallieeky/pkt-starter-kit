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
        try {
            $method = $method ?? config('crypt.cipher');
            $options = config('crypt.options') ?? 0;
            $iv = $iv ?? config('crypt.iv');
            $key = $key ?? config('crypt.key');

            if (is_null($key) || is_null($iv)) {
                throw new \Exception("Key and IV must be set.");
            }
    
            $string = is_array($string) || is_object($string) ? json_encode($string) : $string;
            $encryptedData = openssl_encrypt($string, $method, $key, $options, $iv);
            return $encryptedData;
        }  catch (\Exception $e) {
            return $e->getMessage();
        }
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
        try {
            $method = $method ?? config('crypt.cipher');
            $options = config('crypt.options') ?? 0;
            $iv = $iv ?? config('crypt.iv');
            $key = $key ?? config('crypt.key');

            if (is_null($key) || is_null($iv)) {
                throw new \Exception("Key and IV must be set.");
            }
    
            $decryptedData = openssl_decrypt($string, $method, $key, $options, $iv);
            return $decryptedData;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Check if the given value is valid.
     *
     * @param string $value
     * 
     * @return bool
     */
    public static function isValid($value): bool
    {
        $decrypted = self::decrypt($value);
        return $decrypted !== "";
    }

    /**
     * Check if the given value is invalid.
     *
     * @param string $value
     * 
     * @return bool
     */
    public static function isInvalid($value): bool
    {
        $decrypted = self::decrypt($value);
        return $decrypted === "";
    }

    /**
     * Check if the given value is same as the encrypted value.
     *
     * @param string $value
     * @param string $encryptedValue
     * 
     * @return bool
     */
    public static function check($value, $encryptedValue): bool
    {
        $decrypted = self::decrypt($encryptedValue);
        return $decrypted === $value;
    }

}
