<?php

namespace Framework;



class Session
{
    const CIPHERING = "aes-128-cbc";
    // Using OpenSSl Encryption method
    const IV = "vbuHTFDrWC7ML5==";

    const ENCRYPTIONKEY = "OCR-P5-blog";

    public static function setSessionValue(string $key, string $value)
    {
        $_SESSION[$key] = self::cryptData($value);
        return true;
    }

    public static function getSessionByKey(string $key)
    {
        if (self::checkSessionKey($key)) {
            return self::decryptData($_SESSION[$key]);
        }
        return false;
    }

    public static function checkSessionKey(string $key)
    {
        return isset($_SESSION[$key]);
    }

    private static function cryptData(string $data): string
    {
        return openssl_encrypt($data, self::CIPHERING, self::ENCRYPTIONKEY, OPENSSL_RAW_DATA, self::IV );
    }

    private static function decryptData(string $data): string
    {
        return openssl_decrypt($data, self::CIPHERING, self::ENCRYPTIONKEY, OPENSSL_RAW_DATA, self::IV );
    }
}
