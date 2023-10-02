<?php

namespace Framework;

use PhpParser\JsonDecoder;

class PDOConnection
{
    private $dbConnect;
    private static $pdoInstance;
    
    public static function getInstance(array $datasource)
    {
        if(empty(self::$pdoInstance)){
            self::$pdoInstance = new PDOConnection($datasource);
        }
        return self::$pdoInstance->dbConnect;
    }
    
    private function __construct($datasource)
    {
        $dsn = $datasource['dbname'];
        if (isset($datasource['host'])){
            $dsn .= " ; " .$datasource['host']; 
        }
        if (isset($datasource['port'])){
            $dsn .= " ; " . $datasource['port'];
        }
        echo "$dsn";
        $this->dbConnect = new \PDO($dsn, $datasource['username'], $datasource['password'], );
                                        //  $datasource['options']
    }
}
