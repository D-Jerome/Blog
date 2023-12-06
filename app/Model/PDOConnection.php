<?php

namespace App\Model;

use Framework\Exception\DataBaseConnectionException;
use PDO;
use PhpParser\json_decode;

class PDOConnection
{
    /**
     * database connector
     *
     * @var PDO
     */
    private PDO $dbConnect;

    /**
     * Instance of connection
     *
     * @var PDOConnection
     */
    private static ?PDOConnection $pdoInstance;


    /**
     * getInstance : create instance pdo if no set
     *
     * @param  array<string,string> $datasource Database connection informations from config file
     * @return PDO
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
     * @param  array<string, string> $datasource : database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {

            $dsn = ($datasource['dbtype']);
            $dsn .= ($datasource['host']);
            $dsn .= '; ' . ($datasource['dbname']);
            $dsn .= "; " . ($datasource['port']);
            $this->dbConnect = new PDO(
                $dsn,
                $datasource['username'],
                $datasource['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS
                ]
            );
       
        $this->dbConnect->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

    }//end __construct

}
