<?php

namespace Framework\Helpers;

class Text 
{
    public static function toSlug($text)
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        
    }

    public static function sanitizeMatches(string $string): string
    {
        return rtrim($string,'-'); 
    }

}