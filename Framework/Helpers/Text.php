<?php

namespace Framework\Helpers;

class Text
{

    /**
     * toSlug: create a slug text of the @param
     *
     * @param  string $text text to transform
     * @return string
     */
    public static function toSlug(string $text): string
    {
        return \Safe\preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
    }


    /**
     * camelCaseToSnakeCase : change type of style of text
     *
     * @param  string $string text to transform
     * @return string
     */
    public static function camelCaseToSnakeCase(string $string): string
    {

        return strtolower(\Safe\preg_replace('/(?<!^)[A-Z]/', '_$0', $string));

    }


}
