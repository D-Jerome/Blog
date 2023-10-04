<?php

namespace Framework;

use Exception;
use PDO;
use PhpParser\json_decode;


class PDOConnection
{
    private $dbConnect;
    private static $pdoInstance;


    public static function getInstance(array $datasource)
    {

        if (empty(self::$pdoInstance) || (!isset(self::$pdoInstance))) {
            self::$pdoInstance = new PDOConnection($datasource);
        }

        return self::$pdoInstance->dbConnect;
    }

    private function __construct($datasource)
    {
        $dsn = $datasource['dbtype'];
        if (isset($datasource['host'])) {
            $dsn .= $datasource['host'];
        }
        if (isset($datasource['dbname'])) {
            $dsn .= "; " . $datasource['dbname'];
        }
        if (isset($datasource['port'])) {
            $dsn .= "; " . $datasource['port'];
        }

        $this->dbConnect = new PDO($dsn, $datasource['username'], $datasource['password'] /*,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]*/);
    }
}
