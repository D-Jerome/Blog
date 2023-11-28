<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use Framework\PDOConnection;
use PDO;

class RoleManager extends BaseManager
{

    private static ?BaseManager $roleInstance;


    /**
     * __construct
     *
     * @param  array<string, string> $datasource Database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {
        parent::__construct('role', Role::class, $datasource);

    }//end __construct


    /**
     * Instance of manager
     *
     * @param array<string, array<string>|string> $datasource
     *
     * @return object
     */
    public static function getRoleInstance(array $datasource): object
    {
        if (empty(self::$roleInstance) || (!isset(self::$roleInstance))) {
            self::$roleInstance = new self($datasource);
        }

        return self::$roleInstance;
    }

}
