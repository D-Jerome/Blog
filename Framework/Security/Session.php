<?php

namespace Framework\Security;

use App\Model\Entities\User;

class Session
{
    const CIPHERING = "aes-128-cbc";
    // Using OpenSSl Encryption method
    const IV = "vbuHTFDrWC7ML5==";

    const ENCRYPTIONKEY = "OCR-P5-blog";

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getUser(): ?AuthUser
    {
        return isset($_SESSION['id']) ?   new AuthUser($_SESSION['id'], $_SESSION['role'], $_SESSION['pseudo']) : null;
    }

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
        return openssl_encrypt($data, self::CIPHERING, self::ENCRYPTIONKEY, OPENSSL_RAW_DATA, self::IV);
    }

    private static function decryptData(string $data): string
    {
        return openssl_decrypt($data, self::CIPHERING, self::ENCRYPTIONKEY, OPENSSL_RAW_DATA, self::IV);
    }

    public function connect(User $user)
    {
        $_SESSION['id'] = $user->getId();
        $_SESSION['role'] = $user->getRoleId();
        $_SESSION['pseudo'] = $user->getUsername();
    }
}
