<?php

declare(strict_types=1);

namespace Framework;

class Config
{
    /**
     * datas from config file
     */
    protected static object $config;

    /**
     * Base url of site
     */
    private static string $baseUrl;

    /**
     * config Database of site
     *
     * @var array<string,string>
     */
    private static array $databaseConf;

    /**
     * email config of site
     *
     * @var array<string,int|bool|string>
     */
    private static array $emailConf;

    /**
     * Base url of site
     */
    private static object $filterConf;

    public function __construct()
    {
        self::$config = (object) \Safe\json_decode(\Safe\file_get_contents(__DIR__.'/../config/config.json'), false);
        self::$baseUrl = self::$config->baseUrl;
        self::$databaseConf = (array) self::$config->database;
        self::$emailConf = (array) self::$config->email;
        self::$filterConf = self::$config->filter;
    }
    // end __construct()

    /**
     * Return Base of url site
     */
    public static function getBaseUrl(): string
    {
        return self::$baseUrl;
    }

    /**
     * Obtain result for only 1 specific data
     */
    public static function getSpecificData(string $category, string $page, string $needle): false | object
    {
        if (false === isset(self::$config->{$category})) {
            return false;
        }
        if (false === isset(self::$config->{$category}->{$page})) {
            return false;
        }
        if (false === isset(self::$config->{$category}->{$page}->{$needle})) {
            return false;
        }

        return self::$config->{$category}->{$page}->{$needle};
    }

    /**
     * getDatasource : get the config information of database in array
     *
     * @return array<string,string>
     */
    public static function getDatasource(): array
    {
        return self::$databaseConf;
    }

    /**
     * getEmailSource: get the config information of email in array
     *
     * @return array<string,bool|int|string>
     */
    public static function getEmailSource(): array
    {
        return self::$emailConf;
    }
}

//  Recuperer les info du fichier de configuration //
//  stocker le fichier //
//  spliter les informations en fonction de la categorie passé en parametre
//  Renvoyer les données spliter dans un array simple
