<?php

namespace Framework;



class Session
{

    public static function setSessionValue(string $key, string $value)
    {
        echo 'session' . $key;
        $_SESSION[$key] = serialize($value);
        var_dump($_SESSION[$key],'|');
        return true;
    }

    public static function getSessionByKey(string $key)
    {
        if (self::checkSessionKey($key)) {
            return $_SESSION[$key];
        }
        return false;
    }

    public static function checkSessionKey(string $key)
    {
        echo 'check|';
        var_dump($key,'|');
        var_dump(isset($_SESSION[$key]),'|');
        return isset($_SESSION[$key]);
    }


}
