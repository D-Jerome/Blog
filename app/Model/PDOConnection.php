<?php

namespace App\Model;

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
     * @param  array<string, array<string>|string> $datasource Database connection informations from config file
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
     * @param  array<string, array<string>|string> $datasource : database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {
        $dsn = $datasource['dbtype'];
        if (isset($datasource['host'])) {
            $dsn .= $datasource['host'];
        }
        if (isset($datasource['dbname'])) {
            $dsn .= '; ' . $datasource['dbname'];
        }
        if (isset($datasource['port'])) {
            $dsn .= "; " . $datasource['port'];
        }

        $this->dbConnect = new PDO(
            $dsn,
            $datasource['username'],
            $datasource['password'],
            $datasource['options']
        );

        $this->dbConnect->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

    }//end __construct

}
