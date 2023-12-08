<?php

namespace Framework;

use Webmozart\Assert\Assert;

use function Safe\parse_url;

class Config
{
    /**
     * datas from config file
     *
     * @var array<string,mixed>
     */
    protected static array $config;

    /**
     * Base url of site
     *
     * @var string
     */
    public static string $baseUrl;

    public function __construct()
    {
        self::$config = \Safe\json_decode(\Safe\file_get_contents(__DIR__ . "/../config/config.json"), true);
        self::$baseUrl = self::$config['baseUrl'];
    }
    //end __construct()

    /**
     * Return Base of url site
     *
     * @return string
     *
     */
    public static function getBaseUrl(): string
    {
        return self::$baseUrl;
    }

    /**
     * Obtain result for only 1 specific data
     *
     * @param string $category
     * @param string $page
     * @param string $needle
     *
     * @return array<string,string>|false|null
     *
     */
    public static function getSpecificData(string $category, string $page, string $needle): null|array|false
    {
        if (array_key_exists($category, self::$config) === false) {
            return false;
        }
        if (array_key_exists($page, self::$config[$category]) === false) {
            return false;
        }
        if (array_key_exists($needle, self::$config[$category][$page]) === false) {
            return false;
        }
        return self::$config[$category][$page][$needle];
    }


    /**
         * getDatasource : get the config information of database in array
         *
         * @return array<string,string>
         */
    public static function getDatasource(): array
    {
        return self::$config['database'];
    }


    /**
     * getEmailSource: get the config information of email in array
     *
     * @return array<string,bool|int|string>
     */
    public static function getEmailSource(): array
    {
        return self::$config['email'];
    }
}


//  Recuperer les info du fichier de configuration //
//  stocker le fichier //
//  spliter les informations en fonction de la categorie passé en parametre
//  Renvoyer les données spliter dans un array simple
