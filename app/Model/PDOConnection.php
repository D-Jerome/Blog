<?php

namespace App\Model;

use Exception;
use PDO;
use PhpParser\json_decode;


class PDOConnection
{
    
    private $dbConnect;
    
    private static $pdoInstance;

    
    /**
     * getInstance : create instance pdo if no set
     *
     * @param  array $datasource
     * @return void
     */
    public static function getInstance(array $datasource)
    {

        if (empty(self::$pdoInstance) || (!isset(self::$pdoInstance))) {
            self::$pdoInstance = new PDOConnection($datasource);
        }

        return self::$pdoInstance->dbConnect;
    }

    
    /**
     * __construct
     *
     * @param  array $datasource
     * @return void
     */
    private function __construct(array $datasource)
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

        $this->dbConnect = new PDO($dsn, $datasource['username'], $datasource['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS
        ]);

        $this->dbConnect->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    } //end __construct

    
}
