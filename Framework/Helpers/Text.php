<?php

namespace Framework\Helpers;

class Text 
{
    public static function toSlug($text)
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        
    }

    public static function camelCaseToSnakeCase(string $string): string
    {

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
        
    }
}